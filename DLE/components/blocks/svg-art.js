(function() {
  if (window.BlockRegistry && window.BlockRegistry['svg-art']) {
    return;
  }

  if (typeof window.Block === 'undefined') {
    console.error('❌ ОШИБКА: window.Block не найден.');
    return;
  }

  // === ПРЕСЕТЫ SVG-АРТ ===
  window.SvgArtPresets = [

    {
      name: "🏞️ Минималистичный пейзаж",
      code: `<svg viewBox="0 0 400 250" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="sky" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#87ceeb"/>
      <stop offset="100%" stop-color="#e0f6ff"/>
    </linearGradient>
    <radialGradient id="sun" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#fff4e6"/>
      <stop offset="100%" stop-color="#fbbf24"/>
    </radialGradient>
  </defs>
  <rect width="400" height="250" fill="url(#sky)"/>
  <circle cx="320" cy="60" r="30" fill="url(#sun)"/>
  <path d="M0 180 Q100 140 200 170 T400 160 V250 H0 Z" fill="#a8d5a2" opacity="0.7"/>
  <path d="M0 210 Q150 180 300 200 T400 190 V250 H0 Z" fill="#5ba55b"/>
</svg>`
    },
	
	
	
	 {
	
	  name: "🏞️ Ночной пейзаж",
      code: `<svg viewBox="0 0 400 250" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="nightSky" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#0f172a"/>
      <stop offset="100%" stop-color="#312e81"/>
    </linearGradient>
    <linearGradient id="lake" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#1e1b4b"/>
      <stop offset="100%" stop-color="#312e81"/>
    </linearGradient>
  </defs>
  
  <!-- Небо -->
  <rect width="400" height="250" fill="url(#nightSky)"/>
  
  <!-- Звезды -->
  <circle cx="50" cy="40" r="1.5" fill="#ffffff" opacity="0.8"/>
  <circle cx="120" cy="70" r="1" fill="#ffffff" opacity="0.6"/>
  <circle cx="200" cy="30" r="1.5" fill="#ffffff" opacity="0.9"/>
  <circle cx="280" cy="50" r="1" fill="#ffffff" opacity="0.7"/>
  <circle cx="350" cy="80" r="1.5" fill="#ffffff" opacity="0.8"/>
  <circle cx="90" cy="100" r="1" fill="#ffffff" opacity="0.5"/>
  <circle cx="310" cy="20" r="1" fill="#ffffff" opacity="0.6"/>
  
  <!-- Луна -->
  <circle cx="320" cy="60" r="25" fill="#f8fafc"/>
  <circle cx="330" cy="55" r="20" fill="url(#nightSky)"/>
  
  <!-- Дальние горы -->
  <path d="M0 180 L100 100 L200 160 L300 90 L400 150 V250 H0 Z" fill="#1e1b4b" opacity="0.8"/>
  
  <!-- Ближние холмы/лес -->
  <path d="M0 200 Q80 160 150 190 T300 170 T400 190 V250 H0 Z" fill="#0f172a"/>
  
  <!-- Озеро -->
  <rect x="0" y="210" width="400" height="40" fill="url(#lake)" opacity="0.6"/>
  
  <!-- Отражение луны в озере -->
  <ellipse cx="320" cy="225" rx="15" ry="3" fill="#f8fafc" opacity="0.3"/>
  <ellipse cx="320" cy="235" rx="10" ry="2" fill="#f8fafc" opacity="0.2"/>
</svg>`
},
    {
      name: "🎵 TEST Ноты ",
      code: `<svg viewBox="0 0 600 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
  
  <!-- Нотный стан (остается на месте, большой и четкий) -->
  <g stroke="#000000" stroke-width="0.3" stroke-linecap="round">
    <line x1="60" y1="103" x2="572" y2="103" />
    <line x1="60" y1="121.5" x2="572" y2="121.5" />
    <line x1="60" y1="140" x2="572" y2="140" />
    <line x1="60" y1="158.5" x2="572" y2="158.5" />
    <line x1="60" y1="177" x2="572" y2="177" />
  </g>

  <!-- ВОТ ЗДЕСЬ МЫ ИГРАЕМ! -->
  <g transform="scale(0.5) translate(300, 120)">
    
    <!-- 1. Половинка -->
    <g>
      <ellipse stroke-width="4" stroke="#000000" fill="none" transform="rotate(-25 121 141)" ry="16" rx="24" cy="141" cx="121"/>
      <line stroke-linecap="round" stroke-width="5" stroke="#000000" y2="44" x2="144" y1="132" x1="144"/>
    </g>
    
    <!-- 2. Четверть -->
    <g>
      <ellipse fill="#000000" transform="rotate(-25 259.5 140.5)" ry="16" rx="24" cy="140.5" cx="259.5"/>
      <line stroke-linecap="round" stroke-width="5" stroke="#000000" y2="44.5" x2="280" y1="132.5" x1="280"/>
    </g>
    
    <!-- 3. Две восьмые с балкой -->
    <g>
      <ellipse fill="#000000" transform="rotate(-25 380 140)" ry="16" rx="24" cy="140" cx="380"/>
      <line stroke-linecap="round" stroke-width="5" stroke="#000000" y2="43.5" x2="400.5" y1="131.5" x1="400.5"/>
      <ellipse fill="#000000" transform="rotate(-25 480 140)" ry="16" rx="24" cy="140" cx="480"/>
      <line stroke-linecap="round" stroke-width="5" stroke="#000000" y2="44" x2="500.5" y1="132" x1="500.5"/>
      <rect fill="#000000" height="12" width="105" y="37" x="398"/>
    </g>

  </g>
</svg>`	
	},
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	 {
	
	  name: "Ноты с бочкой",
      code: `<svg viewBox="0 0 400 250" xmlns="http://www.w3.org/2000/svg">
	
	
	<svg viewBox="0 0 600 200" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">	
	
	<svg width="600" height="200" xmlns="http://www.w3.org/2000/svg">
 <!-- Нотный стан (остается на месте, большой и четкий) -->

 <!-- ВОТ ЗДЕСЬ МЫ ИГРАЕМ! -->
 <g>
  <title>background</title>
  <rect x="-1" y="-1" width="286.35877" height="96.78626" id="canvas_background" fill="none"/>
 </g>
 <g>
  <title>Layer 1</title>
  <g stroke="#000000" stroke-width="0.3" stroke-linecap="round" id="svg_1">
   <line x1="60" y1="103" x2="572" y2="103" id="svg_2"/>
   <line x1="60" y1="121.5" x2="572" y2="121.5" id="svg_3"/>
   <line x1="60" y1="140" x2="572" y2="140" id="svg_4"/>
   <line x1="60" y1="158.5" x2="572" y2="158.5" id="svg_5"/>
   <line x1="60" y1="177" x2="572" y2="177" id="svg_6"/>
  </g>
  <g id="svg_7">
   <!-- 1. Половинка -->
   <!-- 2. Четверть -->
   <!-- 3. Две восьмые с балкой -->
   <g id="svg_14" stroke="null">
    <ellipse fill="#666666" transform="matrix(0.45315389351832497,-0.20668924500068953,0.21130913087034972,0.44324651629887435,-161.7817578588124,29.412407464595077) " ry="16" rx="24" cy="579.85628" cx="573.31695" id="svg_15" stroke="#666666"/>
    <line stroke="#666666" fill="#666666" stroke-linecap="round" stroke-width="2" y2="70.77186" x2="231.62166" y1="163.28588" x1="231.62166" id="svg_16" transform="rotate(0.396337628364563 231.6216735839851,117.02887725830004) "/>
    <ellipse fill="#666666" transform="matrix(0.45315389351832497,-0.20668924500068953,0.21130913087034972,0.44324651629887435,-157.09714721064495,50.08133196466403) " ry="16" rx="24" cy="512.79311" cx="706.57113" id="svg_17" stroke="null"/>
    <line fill="#666666" stroke-linecap="round" stroke-width="2" y2="72.29086" x2="281.69618" y1="127.42189" x1="281.69618" id="svg_18" stroke="#666666"/>
    <rect fill="#666666" height="5.86882" width="51.83356" y="67.77911" x="231.07333" id="svg_19" stroke="null"/>
   </g>
   <g id="svg_35">
    <ellipse fill="#EDB6DC" transform="matrix(0.45315389351832497,-0.21130913087034972,0.21130913087034972,0.45315389351832497,-167.53236825528947,1.416597421531094) " ry="16" rx="24" cy="500.88212" cx="464.14575" id="svg_33" stroke="#EDB6DC"/>
    <line stroke-linecap="round" stroke-width="2" y2="68.36157" x2="158.88805" y1="126.31504" x1="158.88805" id="svg_34" fill="#EDB6DC" stroke="#EDB6DC"/>
   </g>
   <g id="svg_47" stroke="null">
    <ellipse fill="#000000" transform="matrix(0.45315389351832497,-0.20668924500068953,0.21130913087034972,0.44324651629887435,-161.7817578588124,29.412407464595077) " ry="16" rx="24" cy="681.19415" cx="967.70833" id="svg_42" stroke="null"/>
    <line stroke-linecap="round" stroke-width="2" y2="70.17349" x2="430.97988" y1="127.17752" x1="430.97988" id="svg_43" transform="rotate(0.396337628364563 430.98056030273256,98.67550659179167) " stroke="#000000"/>
    <ellipse fill="#000000" transform="matrix(0.45315389351832497,-0.20668924500068953,0.21130913087034972,0.44324651629887435,-157.09714721064495,50.08133196466403) " ry="16" rx="24" cy="681.19415" cx="1067.70833" id="svg_44" stroke="null"/>
    <line stroke-linecap="round" stroke-width="2" y2="72.29086" x2="480.93158" y1="127.42189" x1="480.93158" id="svg_45" stroke="#000000"/>
    <rect fill="#000000" height="5.86882" width="51.83356" y="67.77911" x="430.1467" id="svg_46" stroke="null"/>
   </g>
   <g id="svg_50">
    <ellipse fill="#EDB6DC" transform="matrix(0.45315389351832497,-0.21130913087034972,0.21130913087034972,0.45315389351832497,-167.53236825528947,1.416597421531094) " ry="16" rx="24" cy="669.28316" cx="825.28295" id="svg_48" stroke="#EDB6DC"/>
    <line stroke-linecap="round" stroke-width="2" y2="68.36157" x2="358.12345" y1="126.31504" x1="358.12345" id="svg_49" fill="#EDB6DC" stroke="#EDB6DC"/>
   </g>
  </g>
 </g>
</svg>`	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
  ];
	


  class SvgArtBlock extends window.Block {
    render() {
      this.element = document.createElement('div');
      this.element.className = 'le-svg-art-block';
      
      // Создаем изолированный Shadow DOM
      this.shadow = this.element.attachShadow({ mode: 'open' });
      
      // Добавляем стили, гарантирующие, что SVG НИКОГДА не сломает карточку
      const style = document.createElement('style');
      style.textContent = `
        :host {
          display: block;
          width: 100%;
          margin: 10px 0;
        }
        svg {
          width: 100%;
          height: auto;
          max-width: 100%;
          display: block;
        }
        .error-msg {
          color: #ef4444;
          padding: 20px;
          text-align: center;
          font-family: system-ui, sans-serif;
          background: #fef2f2;
          border-radius: 8px;
          border: 1px solid #fecaca;
        }
      `;
      this.shadow.appendChild(style);
      
      // Вставляем код из DSL с простой валидацией
      if (this.data.code && this.data.code.trim().toLowerCase().includes('<svg')) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = this.data.code.trim();
        this.shadow.appendChild(wrapper);
      } else {
        const error = document.createElement('div');
        error.className = 'error-msg';
        error.textContent = 'Код иллюстрации не указан или не является валидным SVG';
        this.shadow.appendChild(error);
      }
      
      this.engine.els.blocksContainer.appendChild(this.element);
    }
    
    destroy() {
      if (this.element) this.element.remove();
    }
  }

  window.BlockRegistry['svg-art'] = SvgArtBlock;
  console.log("✅ svg-art.js выполнен: блок 'svg-art' зарегистрирован, пресеты загружены:", window.SvgArtPresets.length - 1);
})();