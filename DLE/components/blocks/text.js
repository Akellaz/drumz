(function() {
  if (window.BlockRegistry && window.BlockRegistry['text']) {
    return;
  }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден.');
    return;
  }

  class TextBlock extends window.Block {
    render() {
      const variant = this.data.variant || 'paragraph';
      if (variant === 'heading') {
        this.element = document.createElement('h2');
        this.element.className = 'le-text-heading';
      } else {
        this.element = document.createElement('p');
        this.element.className = 'le-text-paragraph';
      }
      this.element.textContent = this.data.text || '';
      this.engine.els.blocksContainer.appendChild(this.element);
    }
  }

  window.BlockRegistry['text'] = TextBlock;
  console.log("✅ text.js выполнен: блок 'text' зарегистрирован");
})();