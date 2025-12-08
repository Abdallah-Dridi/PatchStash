<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Module;
use App\Entity\PatchCycle;
use App\Entity\Project;
use App\Entity\Report;
use App\Entity\User;
use App\Entity\Vulnerability;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class ExportController extends AbstractController
{
    #[Route('/export/{entity}.{_format}', name: 'app_export_entity', requirements: ['_format' => 'json|csv'], methods: ['GET'])]
    public function export(string $entity, string $_format, ManagerRegistry $doctrine): Response
    {
        $entity = strtolower($entity);
        $format = strtolower($_format);

        $mapping = [
            'users' => User::class,
            'projects' => Project::class,
            'modules' => Module::class,
            'assets' => Asset::class,
            'patch_cycles' => PatchCycle::class,
            'vulnerabilities' => Vulnerability::class,
            'reports' => Report::class,
        ];
        $now = (new \DateTimeImmutable())->format('Ymd-His');

        if ($entity === 'all') {
            $all = [];
            foreach ($mapping as $key => $class) {
                $repo = $doctrine->getRepository($class);
                $objects = $repo->findAll();
                $all[$key] = array_map([$this, 'objectToArray'], $objects);
            }

            return $this->formatResponse($all, 'all-'.$now, $format);
        }

        if (!isset($mapping[$entity])) {
            return new Response('Unknown entity', Response::HTTP_BAD_REQUEST);
        }

        $repo = $doctrine->getRepository($mapping[$entity]);
        $items = $repo->findAll();
        $rows = array_map([$this, 'objectToArray'], $items);

        return $this->formatResponse($rows, $entity.'-'.$now, $format);
    }

    private function objectToArray(object $obj): array
    {
        $data = [];
        $ref = new \ReflectionClass($obj);
        foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();
            if (strpos($name, 'get') !== 0) {
                continue;
            }
            if ($method->getNumberOfRequiredParameters() > 0) {
                continue;
            }
            try {
                $key = lcfirst(substr($name, 3));
                $value = $method->invoke($obj);
                if ($value instanceof \DateTimeInterface) {
                    $value = $value->format('c');
                } elseif (is_object($value)) {
                    if (method_exists($value, '__toString')) {
                        $value = (string) $value;
                    } else {
                        // for associations, try retrieving id if exists
                        if (method_exists($value, 'getId')) {
                            $value = $value->getId();
                        } else {
                            $value = null;
                        }
                    }
                }
                $data[$key] = $value;
            } catch (\Throwable $e) {
                // ignore problematic getters
            }
        }

        return $data;
    }

    private function formatResponse(array $rows, string $basename, string $format): Response
    {
        $filename = sprintf('%s.%s', $basename, $format);

        if ($format === 'json') {
            $content = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $headers = ['Content-Type' => 'application/json', 'Content-Disposition' => 'attachment; filename="'.$filename.'"'];
            return new Response($content, 200, $headers);
        }

        if ($format === 'csv') {
            // If top-level keys are entity names (for 'all'), create separate CSV files and bundle into a ZIP
            if ($this->isAssocArrayOfArrays($rows)) {
                $zip = new \ZipArchive();
                $tmpFile = tempnam(sys_get_temp_dir(), 'export_') . '.zip';
                if ($zip->open($tmpFile, \ZipArchive::CREATE) !== true) {
                    return new Response('Could not create ZIP archive', Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                foreach ($rows as $section => $items) {
                    $csv = $this->arrayToCsv($items);
                    $zip->addFromString($section . '.csv', $csv);
                }

                $zip->close();

                $content = file_get_contents($tmpFile);
                unlink($tmpFile);

                $zipName = sprintf('%s.zip', $basename);
                $headers = [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="'.$zipName.'"',
                    'Content-Length' => strlen($content),
                ];
                return new Response($content, 200, $headers);
            }

            // Single entity CSV
            $content = $this->arrayToCsv($rows);
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Content-Length' => strlen($content),
            ];
            return new Response($content, 200, $headers);
        }

        return new Response('Unsupported format', Response::HTTP_BAD_REQUEST);
    }

    private function isAssocArrayOfArrays(array $arr): bool
    {
        // associative with arrays as values
        foreach ($arr as $k => $v) {
            if (!is_array($v)) {
                return false;
            }
            return true;
        }
        return false;
    }

    private function arrayToCsv(array $rows): string
    {
        if (count($rows) === 0) {
            return '';
        }
        $fp = fopen('php://memory', 'r+');
        $headers = array_keys(reset($rows));
        fputcsv($fp, $headers);
        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $h) {
                $value = $row[$h] ?? '';
                $line[] = $this->valueToString($value);
            }
            fputcsv($fp, $line);
        }
        rewind($fp);
        $content = stream_get_contents($fp);
        fclose($fp);
        return $content;
    }

    private function arrayToHtmlTable(array $rows): string
    {
        if (count($rows) === 0) {
            return '<p>No rows</p>';
        }
        $headers = array_keys(reset($rows));
        $html = '<table border="1" cellpadding="4" cellspacing="0"><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>'.htmlspecialchars((string) $h).'</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($headers as $h) {
                $value = $this->valueToString($row[$h] ?? '');
                $html .= '<td>'.htmlspecialchars($value).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    private function valueToString($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
            return get_class($value);
        }
        return (string) $value;
    }
}
