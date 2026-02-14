import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
  static values = { text: String };

  connect() {
    this._t = null;
  }

  async copy(event) {
    const button = event.currentTarget;
    const originalLabel = button.textContent.trim();
    const text = this.textValue;

    button.disabled = true;

    try {
      await this.copyToClipboard(text);

      button.textContent = "Copié ✔";
      button.classList.add("is-copied");

      clearTimeout(this._t);
      this._t = setTimeout(() => {
        button.textContent = originalLabel;
        button.classList.remove("is-copied");
        button.disabled = false;
      }, 1800);

    } catch (e) {
      button.textContent = "Erreur";
      button.classList.add("is-error");

      clearTimeout(this._t);
      this._t = setTimeout(() => {
        button.textContent = originalLabel;
        button.classList.remove("is-error");
        button.disabled = false;
      }, 2200);
    }
  }

  async copyToClipboard(text) {
    // Clipboard API (secure context required)
    if (navigator.clipboard && window.isSecureContext) {
      return navigator.clipboard.writeText(text);
    }

    // Fallback: execCommand
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "fixed";
    textarea.style.top = "-9999px";
    textarea.style.left = "-9999px";
    document.body.appendChild(textarea);

    textarea.select();
    textarea.setSelectionRange(0, textarea.value.length);

    const ok = document.execCommand("copy");
    document.body.removeChild(textarea);

    if (!ok) throw new Error("execCommand copy failed");
  }
}