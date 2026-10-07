<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php
// tests/index.php
require_once __DIR__ . '/../../includes/seo.php';
?>
  <link rel="stylesheet" href="/assets/style.css?v=<?= time() ?>">
  <style>
    .rhythm-test-container {
      max-width: 800px;
      margin: 0 auto;
      padding: 0 20px;
    }
    .rhythm-test-container h1 {
      text-align: center;
      margin: 20px 0 15px;
      color: #4a5568;
      font-size: 24px;
    }
    .mode-selector {
      text-align: center;
      margin-bottom: 15px;
    }
    .mode-btn {
      padding: 4px 10px;
      margin: 0 4px;
      background: #f7fafc;
      border: 1px solid #e2e8f0;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      transition: all 0.2s;
      color: #718096;
    }
    .mode-btn:hover {
      background: #edf2f7;
    }
    .mode-btn.active {
      background: #90cdf4;
      color: #2d3748;
      border-color: #90cdf4;
    }
    #questionBox {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      text-align: center;
    }
    .math-example {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      font-size: 28px;
      font-weight: bold;
      min-height: 80px;
      line-height: 1;
      margin-bottom: 20px;
    }
    .rhythm-display {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 60px;
    }
    .abcjs-container .abcjs-staff,
    .abcjs-container .abcjs-top-line,
    .abcjs-container .abcjs-bottom-line,
    .abcjs-container .abcjs-staff-line,
    .abcjs-container .abcjs-staff-extra,
    .abcjs-container .abcjs-bar-number,
    .abcjs-container .abcjs-clef,
    .abcjs-container .abcjs-key-signature,
    .abcjs-container .abcjs-time-signature {
      display: none !important;
    }
    #options {
      display: flex;
      gap: 10px;
      margin: 20px 0;
      justify-content: center;
      flex-wrap: wrap;
    }
    .option-btn {
      padding: 12px 20px;
      background: #edf2f7;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      cursor: pointer;
      text-align: center;
      transition: background 0.2s;
      min-width: 120px;
      flex: 1;
      max-width: 160px;
    }
    .option-btn:hover {
      background: #e2e8f0;
    }
    .option-btn.selected {
      background: #bee3f8;
      border-color: #90cdf4;
    }
    .combinatorics-option {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;
      width: 100%;
      max-width: 600px;
    }
    .combinatorics-item {
      width: 100%;
      text-align: left;
    }
    .combinatorics-label {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      background: #edf2f7;
      border: 1px solid #e2e8f0;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .combinatorics-label:hover {
      background: #e2e8f0;
    }
    .combinatorics-input {
      margin-right: 12px;
      transform: scale(1.3);
    }
    #feedback {
      text-align: center;
      min-height: 24px;
      margin: 15px 0;
      font-weight: bold;
    }
    .correct { color: #38a169; }
    .incorrect { color: #e53e3e; }
    .control-buttons {
      display: flex;
      flex-direction: column;
      gap: 10px;
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
    }
    .btn {
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
    }
    .btn-check {
      background: #3182ce;
      color: white;
    }
    .btn-next {
      background: #38a169;
      color: white;
      display: none;
    }
    .btn:disabled {
      background: #a0aec0;
      cursor: not-allowed;
      opacity: 0.7;
    }
    /* Адаптация для мобильных */
    @media (max-width: 600px) {
      #options {
        flex-direction: column;
      }
      .option-btn {
        max-width: none;
      }
      .math-example {
        gap: 10px;
        font-size: 24px;
      }
      .rhythm-display {
        height: 50px;
      }
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/../../includes/header.php'; ?>

  <main class="container rhythm-test-container">
    <h1>Математика Ритма</h1>

    <div class="mode-selector">
      <button id="modeFirstLook" class="mode-btn">Первый взгляд</button>
      <button id="modeArith" class="mode-btn">Арифметика (a + b = ?)</button>
      <button id="modeAlgebra" class="mode-btn">Алгебра (? + b = c)</button>
      <button id="modeCombinatorics" class="mode-btn active">Комбинаторика</button>
    </div>

    <div id="questionBox">
      <div class="math-example">Загрузка...</div>
      <div id="options"></div>
      <div id="feedback"></div>
      <div class="control-buttons">
        <button id="checkBtn" class="btn btn-check" disabled>Проверить</button>
        <button id="nextBtn" class="btn btn-next">Следующий вопрос</button>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js"></script>
  <script>
    // === Термины для всех режимов ===
    const terms = [
      [1, 'c', 'шестнадцатая'],
      [2, 'c2', 'восьмая'],
      [2, 'cc', 'две шестнадцатых'],
      [3, 'c3', 'восьмая с точкой'],
      [4, 'c4', 'четверть'],
      [4, 'c2c2', 'две восьмых'],
      [4, 'cccc', 'четыре шестнадцатых'],
      [6, 'c6', 'четверть с точкой'],
      [8, 'c8', 'половинка'],
      [8, 'c4c4', 'две четверти'],
      [8, 'c2c2 c2c2', 'четыре восьмых'],
      [8, 'cccc cccc', 'восемь шестнадцатых'],
      [12, 'c12', 'половинка с точкой'],
      [16, 'c16', 'целая']
    ];

    // === Термины ТОЛЬКО для "Первого взгляда" ===
    const firstLookTerms = [
      [16, 'c16', 'целая'],
      [8, 'c8', 'половинка'],
      [4, 'c4', 'четверть'],
      [2, 'c2', 'восьмая'],
      [2, 'c2c2', 'две восьмых'],
      [1, 'c', 'шестнадцатая'],
      [1, 'cccc', 'четыре шестнадцатых'],
	  [16, 'z16', 'целая пауза'],
      [8, 'z8', 'половинная пауза'],
      [4, 'z4', 'четвертная пауза'],
      [2, 'z2', 'восьмая пауза'],
      [1, 'z', 'шестнадцатая пауза']
    ];

    const sumToAbc = {
      1: 'c', 2: 'c2', 3: 'c3', 4: 'c4', 6: 'c6', 8: 'c8', 12: 'c12', 16: 'c16'
    };

    const sumToLabel = {
      1: 'шестнадцатая',
      2: 'восьмая',
      3: 'восьмая с точкой',
      4: 'четверть',
      6: 'четверть с точкой',
      8: 'половинка',
      12: 'половинка с точкой',
      16: 'целая'
    };

    const firstLookLabels = firstLookTerms.map(t => t[2]);
    const arithmeticLabels = Object.values(sumToLabel);

    let currentMode = 'combinatorics';

    function shuffleArray(array) {
      const arr = [...array];
      for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
      }
      return arr;
    }

    function generateOptions(correctLabel, allLabels) {
      const distractors = new Set([correctLabel]);
      while (distractors.size < 4) {
        const label = allLabels[Math.floor(Math.random() * allLabels.length)];
        distractors.add(label);
      }
      return shuffleArray(Array.from(distractors));
    }

    // === Режим: Первый взгляд ===
    function generateNameNoteTask() {
      const note = firstLookTerms[Math.floor(Math.random() * firstLookTerms.length)];
      const abc = `L:1/16\nK:perc\n${note[1]}`;
      const correctLabel = note[2];
      const options = generateOptions(correctLabel, firstLookLabels);
      const correctIndex = options.indexOf(correctLabel);
      return { mode: 'firstLook', abc, options, correctIndex };
    }

    // === Режим: Арифметика / Алгебра ===
    function generateArithmeticTask() {
      let a, b, sum;
      let attempts = 0;
      const maxAttempts = 100;
      do {
        a = terms[Math.floor(Math.random() * terms.length)];
        b = terms[Math.floor(Math.random() * terms.length)];
        sum = a[0] + b[0];
        attempts++;
        if (attempts > maxAttempts) {
          const correctLabel = 'половинка';
          const options = generateOptions(correctLabel, arithmeticLabels);
          const correctIndex = options.indexOf(correctLabel);
          return {
            mode: 'arithmetic',
            abcA: 'L:1/16\nK:perc\nc4',
            abcB: 'L:1/16\nK:perc\nc4',
            abcSum: 'L:1/16\nK:perc\nc8',
            abcAnswer: 'L:1/16\nK:perc\nc8',
            options,
            correctIndex,
            display: { part1: 'c4', op: '+', part2: 'c4', eq: '=', part3: '?' }
          };
        }
      } while (sum > 16 || !(sum in sumToAbc));

      if (currentMode === 'arithmetic') {
        const correctLabel = sumToLabel[sum];
        const options = generateOptions(correctLabel, arithmeticLabels);
        const correctIndex = options.indexOf(correctLabel);
        return {
          mode: 'arithmetic',
          abcA: `L:1/16\nK:perc\n${a[1]}`,
          abcB: `L:1/16\nK:perc\n${b[1]}`,
          abcSum: `L:1/16\nK:perc\n${sumToAbc[sum]}`,
          abcAnswer: `L:1/16\nK:perc\n${sumToAbc[sum]}`,
          options,
          correctIndex,
          display: { part1: a[1], op: '+', part2: b[1], eq: '=', part3: '?' }
        };
      } else {
        const algebraType = Math.random() < 0.5 ? 'first' : 'second';
        if (algebraType === 'first') {
          const correctLabel = sumToLabel[a[0]];
          const options = generateOptions(correctLabel, arithmeticLabels);
          const correctIndex = options.indexOf(correctLabel);
          return {
            mode: 'algebra',
            subtype: 'first',
            abcA: `L:1/16\nK:perc\n${a[1]}`,
            abcB: `L:1/16\nK:perc\n${b[1]}`,
            abcSum: `L:1/16\nK:perc\n${sumToAbc[sum]}`,
            abcAnswer: `L:1/16\nK:perc\n${sumToAbc[a[0]]}`,
            options,
            correctIndex,
            display: { part1: '?', op: '+', part2: b[1], eq: '=', part3: sumToAbc[sum] }
          };
        } else {
          const correctLabel = sumToLabel[b[0]];
          const options = generateOptions(correctLabel, arithmeticLabels);
          const correctIndex = options.indexOf(correctLabel);
          return {
            mode: 'algebra',
            subtype: 'second',
            abcA: `L:1/16\nK:perc\n${a[1]}`,
            abcB: `L:1/16\nK:perc\n${b[1]}`,
            abcSum: `L:1/16\nK:perc\n${sumToAbc[sum]}`,
            abcAnswer: `L:1/16\nK:perc\n${sumToAbc[b[0]]}`,
            options,
            correctIndex,
            display: { part1: a[1], op: '+', part2: '?', eq: '=', part3: sumToAbc[sum] }
          };
        }
      }
    }

    // === Режим: Комбинаторика ===
    function generateCombinatoricsTask() {
      const targets = [4, 6, 8, 12, 16];
      const target = targets[Math.floor(Math.random() * targets.length)];
      const targetAbc = sumToAbc[target];

      const validCombinations = [];
      for (let i = 0; i < terms.length; i++) {
        if (target === 16 && terms[i][0] < 4) continue;
        for (let j = 0; j < terms.length; j++) {
          if (target === 16 && terms[j][0] < 4) continue;
          if (terms[i][0] + terms[j][0] === target) {
            validCombinations.push({
              abc: `L:1/16\nK:perc\n${terms[i][1]} ${terms[j][1]}`,
              label: `${terms[i][2]} + ${terms[j][2]}`
            });
          }
          if (target < 16) {
            for (let k = 0; k < terms.length; k++) {
              if (terms[i][0] + terms[j][0] + terms[k][0] === target) {
                validCombinations.push({
                  abc: `L:1/16\nK:perc\n${terms[i][1]} ${terms[j][1]} ${terms[k][1]}`,
                  label: `${terms[i][2]} + ${terms[j][2]} + ${terms[k][2]}`
                });
              }
            }
          }
        }
      }

      const uniqueValid = [];
      const seen = new Set();
      for (const combo of validCombinations) {
        if (!seen.has(combo.abc)) {
          seen.add(combo.abc);
          uniqueValid.push(combo);
        }
      }

      if (uniqueValid.length === 0) {
        return {
          mode: 'combinatorics',
          targetAbc: `L:1/16\nK:perc\n${targetAbc}`,
          variants: [
            { abc: `L:1/16\nK:perc\nc8 c8`, label: 'половинка + половинка' },
            { abc: `L:1/16\nK:perc\nc4 c4 c4 c4`, label: 'четыре четверти' },
            { abc: `L:1/16\nK:perc\nc16`, label: 'целая' },
            { abc: `L:1/16\nK:perc\nc2 c2`, label: 'две восьмых' }
          ],
          correctIndices: target === 16 ? [2] : (target === 8 ? [0] : [1])
        };
      }

      const shuffledValid = shuffleArray(uniqueValid);
      const correctCount = Math.min(2, shuffledValid.length);
      const correctVariants = shuffledValid.slice(0, correctCount);

      const invalidVariants = [];
      while (invalidVariants.length < (4 - correctCount)) {
        const a = terms[Math.floor(Math.random() * terms.length)];
        const b = terms[Math.floor(Math.random() * terms.length)];
        const sum2 = a[0] + b[0];
        if (sum2 !== target) {
          invalidVariants.push({
            abc: `L:1/16\nK:perc\n${a[1]} ${b[1]}`,
            label: `${a[2]} + ${b[2]}`
          });
        }
      }

      const allVariants = [...correctVariants, ...invalidVariants.slice(0, 4 - correctCount)];
      const shuffledVariants = shuffleArray(allVariants);

      const correctIndices = [];
      shuffledVariants.forEach((v, i) => {
        if (correctVariants.some(cv => cv.abc === v.abc)) {
          correctIndices.push(i);
        }
      });

      return {
        mode: 'combinatorics',
        targetAbc: `L:1/16\nK:perc\n${targetAbc}`,
        variants: shuffledVariants,
        correctIndices
      };
    }

    // === Основная логика ===
    function generateTask() {
      if (currentMode === 'firstLook') return generateNameNoteTask();
      if (currentMode === 'arithmetic' || currentMode === 'algebra') return generateArithmeticTask();
      if (currentMode === 'combinatorics') return generateCombinatoricsTask();
    }

    // === DOM ===
    const optionsDiv = document.getElementById('options');
    const feedbackDiv = document.getElementById('feedback');
    const checkBtn = document.getElementById('checkBtn');
    const nextBtn = document.getElementById('nextBtn');
    const modeFirstLookBtn = document.getElementById('modeFirstLook');
    const modeArithBtn = document.getElementById('modeArith');
    const modeAlgebraBtn = document.getElementById('modeAlgebra');
    const modeCombinatoricsBtn = document.getElementById('modeCombinatorics');

    let currentTask = null;
    let selectedOption = null;

    function setMode(mode) {
      currentMode = mode;
      modeFirstLookBtn.classList.toggle('active', mode === 'firstLook');
      modeArithBtn.classList.toggle('active', mode === 'arithmetic');
      modeAlgebraBtn.classList.toggle('active', mode === 'algebra');
      modeCombinatoricsBtn.classList.toggle('active', mode === 'combinatorics');
      renderTask();
    }

    function renderTask() {
      currentTask = generateTask();
      optionsDiv.innerHTML = '';
      feedbackDiv.textContent = '';
      feedbackDiv.className = '';
      checkBtn.style.display = 'block';
      nextBtn.style.display = 'none';
      checkBtn.disabled = true;

      const mathContainer = document.querySelector('#questionBox .math-example');
      mathContainer.innerHTML = '';

      if (currentTask.mode === 'firstLook') {
        const div = document.createElement('div');
        div.className = 'rhythm-display';
        ABCJS.renderAbc(div, currentTask.abc, { staffwidth: 150, staffheight: 60, add_classes: true });
        mathContainer.appendChild(div);
        renderSingleChoice();
      } else if (currentTask.mode === 'arithmetic' || currentTask.mode === 'algebra') {
        const { part1, op, part2, eq, part3 } = currentTask.display;
        const createDiv = (content) => {
          const div = document.createElement('div');
          div.className = 'rhythm-display';
          if (content === '?') {
            div.textContent = '?';
            Object.assign(div.style, {
              display: 'flex', justifyContent: 'center', alignItems: 'center',
              height: '60px', minWidth: '60px', fontSize: '28px'
            });
          } else {
            ABCJS.renderAbc(div, `L:1/16\nK:perc\n${content}`, { staffwidth: 150, staffheight: 60, add_classes: true });
          }
          return div;
        };
        const container1 = createDiv(part1);
        const plus = Object.assign(document.createElement('span'), { textContent: op, style: 'font-weight:bold' });
        const container2 = createDiv(part2);
        const equals = Object.assign(document.createElement('span'), { textContent: eq, style: 'font-weight:bold' });
        const container3 = createDiv(part3);
        mathContainer.append(container1, plus, container2, equals, container3);
        renderSingleChoice();
      } else if (currentTask.mode === 'combinatorics') {
        const div = document.createElement('div');
        div.className = 'rhythm-display';
        ABCJS.renderAbc(div, currentTask.targetAbc, { staffwidth: 150, staffheight: 60, add_classes: true });
        mathContainer.appendChild(div);
        renderMultiChoice();
      }
    }

    function renderSingleChoice() {
      optionsDiv.className = '';
      currentTask.options.forEach((text, i) => {
        const btn = document.createElement('button');
        btn.className = 'option-btn';
        btn.textContent = text;
        btn.onclick = () => selectSingleOption(btn, i);
        optionsDiv.appendChild(btn);
      });
    }

    function selectSingleOption(btn, index) {
      document.querySelectorAll('.option-btn').forEach(el => el.classList.remove('selected'));
      btn.classList.add('selected');
      selectedOption = index;
      checkSingleAnswer();
    }

    function checkSingleAnswer() {
      const isCorrect = selectedOption === currentTask.correctIndex;
      feedbackDiv.className = isCorrect ? 'correct' : 'incorrect';
      feedbackDiv.textContent = isCorrect ? '✅ Верно!' : '❌ Неверно. Попробуйте ещё.';
      if (isCorrect) {
        checkBtn.style.display = 'none';
        nextBtn.style.display = 'block';
      } else {
        checkBtn.style.display = 'block';
        nextBtn.style.display = 'none';
      }
    }

    function renderMultiChoice() {
      optionsDiv.className = 'combinatorics-option';
      currentTask.variants.forEach((variant, i) => {
        const item = document.createElement('div');
        item.className = 'combinatorics-item';
        const label = document.createElement('label');
        label.className = 'combinatorics-label';
        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'combinatorics-input';
        input.dataset.index = i;
        const span = document.createElement('span');
        const rhythmDiv = document.createElement('span');
        ABCJS.renderAbc(rhythmDiv, variant.abc, { staffwidth: 200, staffheight: 40, add_classes: true });
        span.appendChild(rhythmDiv);
        label.appendChild(input);
        label.appendChild(span);
        item.appendChild(label);
        optionsDiv.appendChild(item);
      });

      const checkboxes = optionsDiv.querySelectorAll('.combinatorics-input');
      checkboxes.forEach(cb => {
        cb.onchange = () => {
          const anyChecked = Array.from(checkboxes).some(c => c.checked);
          checkBtn.disabled = !anyChecked;
        };
      });
    }

    function checkMultiAnswer() {
      const checkboxes = optionsDiv.querySelectorAll('.combinatorics-input');
      const selectedIndices = Array.from(checkboxes)
        .map((cb, i) => cb.checked ? i : -1)
        .filter(i => i !== -1);

      const correctSet = new Set(currentTask.correctIndices);
      const selectedSet = new Set(selectedIndices);
      const isCorrect = 
        selectedSet.size === correctSet.size &&
        [...selectedSet].every(i => correctSet.has(i));

      feedbackDiv.className = isCorrect ? 'correct' : 'incorrect';
      if (isCorrect) {
        feedbackDiv.textContent = '✅ Все верно! Вы нашли все разложения.';
        checkBtn.style.display = 'none';
        nextBtn.style.display = 'block';
      } else {
        const correctCount = currentTask.correctIndices.length;
        const selectedCorrect = selectedIndices.filter(i => correctSet.has(i)).length;
        feedbackDiv.textContent = `❌ Почти! Верных разложений: ${correctCount}. Вы выбрали ${selectedCorrect}.`;
        checkBtn.style.display = 'block';
        nextBtn.style.display = 'none';
      }
    }

    function nextTask() {
      renderTask();
    }

    // Обработчики режимов
    modeFirstLookBtn.addEventListener('click', () => setMode('firstLook'));
    modeArithBtn.addEventListener('click', () => setMode('arithmetic'));
    modeAlgebraBtn.addEventListener('click', () => setMode('algebra'));
    modeCombinatoricsBtn.addEventListener('click', () => setMode('combinatorics'));

    // Кнопки
    checkBtn.addEventListener('click', () => {
      if (currentTask.mode === 'combinatorics') {
        checkMultiAnswer();
      } else {
        checkSingleAnswer();
      }
    });

    nextBtn.addEventListener('click', nextTask);

    renderTask();
  </script>
</body>
</html>