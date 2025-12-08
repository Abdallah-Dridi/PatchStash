import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['menu', 'token'];
    static values = { entity: String };

    connect() {
        this._boundDoc = this._onDocClick.bind(this);
        document.addEventListener('click', this._boundDoc);
    }

    disconnect() {
        document.removeEventListener('click', this._boundDoc);
    }

    toggle(event) {
        event.stopPropagation();
        const isHidden = this.menuTarget.hasAttribute('hidden');
        if (isHidden) {
            this.show();
        } else {
            this.hide();
        }
    }

    show() {
        this.menuTarget.removeAttribute('hidden');
    }

    hide() {
        this.menuTarget.setAttribute('hidden', '');
    }

    _onDocClick(event) {
        if (!this.element.contains(event.target)) {
            this.hide();
        }
    }

    formatChanged(e) {
        // update CSRF token field when format changes to match token id
        const format = e.target.value;
        if (!this.tokenTarget) return;
        const token = this.tokenTarget.dataset[format + 'Token'];
        if (token) {
            this.tokenTarget.value = token;
        }
    }
}
