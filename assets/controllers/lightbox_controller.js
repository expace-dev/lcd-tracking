import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['modal', 'img'];
  static values = { src: String };

  connect() {
    this._onKeydown = (e) => {
      if (e.key === 'Escape') this.close();
    };

    document.addEventListener("keydown", this.handleKey)
  }


disconnect() {
  document.removeEventListener("keydown", this.handleKey)
}


  open(event) {
    const src = event.currentTarget.dataset.lightboxSrcValue;
    this.imgTarget.src = src;
    this.modalTarget.hidden = false;
    document.body.style.overflow = 'hidden';
    window.addEventListener('keydown', this._onKeydown);
  }

  close() {
    this.modalTarget.hidden = true;
    this.imgTarget.src = '';
    document.body.style.overflow = '';
    window.removeEventListener('keydown', this._onKeydown);
  }
}


