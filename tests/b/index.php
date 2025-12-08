<?php
// tests/index.php
require_once __DIR__ . '/../includes/seo.php';
$title = "Математика Ритма — Арифметика, Алгебра и Первый Взгляд | Drumz.ru";
$description = "Тренируйте ритмическое мышление: узнавайте длительности, складывайте их и решайте уравнения. Учебный тренажёр от Сергея Щепотина.";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
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
    #feedback {
      text-align: center;
      min-height: 24px;
      margin: 15px 0;
      font-weight: bold;
    }
    .correct { color: #38a169; }
    .incorrect { color: #e53e3e; }
    #nextBtn {
      display: block;
      width: 100%;
      padding: 12px;
      margin: 20px 0 10px;
      background: #3182ce;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }
    #nextBtn:disabled {
      background: #a0aec0;
      cursor: not-allowed;
    }
    button:disabled {
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
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="container rhythm-test-container">
    <h1>Математика Ритма</h1>

    <div class="mode-selector">
      <button id="modeFirstLook" class="mode-btn active">Первый взгляд</button>
      <button id="modeArith" class="mode-btn">Арифметика (a + b = ?)</button>
      <button id="modeAlgebra" class="mode-btn">Алгебра (? + b = c)</button>
    </div>

    <div id="questionBox">
      <div class="math-example">Загрузка...</div>
      <div id="options"></div>
      <div id="feedback"></div>
    </div>
    <button id="nextBtn" disabled>Следующий вопрос</button>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/abcjs@6.5.2/dist/abcjs-basic-min.js"></script>
  <script>
    // === Термины для режимов Арифметика / Алгебра (с группировкой и точками) ===
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
      [12, 'c12', 'половинка с точкой']
    ];

    // === Термины ТОЛЬКО для режима "Первый взгляд" (прямое соответствие) ===
    const firstLookTerms = [
      [16, 'c16', 'целая'],
      [8, 'c8', 'половинка'],
      [4, 'c4', 'четверть'],
      [2, 'c2', 'восьмая'],
      [2, 'c2c2', 'две восьмых'],
      [1, 'c', 'шестнадцатая'],
      [1, 'cccc', 'четыре шестнадцатых']
    ];

    const sumToAbc = {
      1: 'c',
      2: 'c2',
      3: 'c3',
      4: 'c4',
      6: 'c6',
      8: 'c8',
      12: 'c12',
      16: 'c16'
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

    // Для "Первого взгляда" используем только названия из firstLookTerms
    const firstLookLabels = firstLookTerms.map(t => t[2]);
    // Для арифметики/алгебры — как раньше
    const arithmeticLabels = Object.values(sumToLabel);

    let currentMode = 'firstLook';

    function generateOptions(correctLabel, allLabels) {
      const distractors = new Set([correctLabel]);
      while (distractors.size < 4) {
        const label = allLabels[Math.floor(Math.random() * allLabels.length)];
        distractors.add(label);
      }
      let options = Array.from(distractors);
      let correctIndex = options.indexOf(correctLabel);

      // Перемешиваем
      for (let i = options.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [options[i], options[j]] = [options[j], options[i]];
        if (correctIndex === i) correctIndex = j;
        else if (correctIndex === j) correctIndex = i;
      }

      return { options, correctIndex };
    }

    function generateNameNoteTask() {
      const note = firstLookTerms[Math.floor(Math.random() * firstLookTerms.length)];
      const abc = `L:1/16\nK:perc\n${note[1]}`;
      const correctLabel = note[2];
      const { options, correctIndex } = generateOptions(correctLabel, firstLookLabels);
      return {
        mode: 'firstLook',
        abc,
        options,
        correctIndex
      };
    }

    function generateTask() {
      if (currentMode === 'firstLook') {
        return generateNameNoteTask();
      }

      // === Старая логика для арифметики/алгебры ===
      let a, b, sum;
      let attempts = 0;
      const maxAttempts = 100;

      do {
        a = terms[Math.floor(Math.random() * terms.length)];
        b = terms[Math.floor(Math.random() * terms.length)];
        sum = a[0] + b[0];
        attempts++;
        if (attempts > maxAttempts) {
          const { options, correctIndex } = generateOptions('половинка', arithmeticLabels);
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
        const { options, correctIndex } = generateOptions(correctLabel, arithmeticLabels);
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
          const { options, correctIndex } = generateOptions(correctLabel, arithmeticLabels);
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
          const { options, correctIndex } = generateOptions(correctLabel, arithmeticLabels);
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

    let currentTask = null;
    const optionsDiv = document.getElementById('options');
    const feedbackDiv = document.getElementById('feedback');
    const nextBtn = document.getElementById('nextBtn');
    const modeFirstLookBtn = document.getElementById('modeFirstLook');
    const modeArithBtn = document.getElementById('modeArith');
    const modeAlgebraBtn = document.getElementById('modeAlgebra');

    function setMode(mode) {
      currentMode = mode;
      modeFirstLookBtn.classList.toggle('active', mode === 'firstLook');
      modeArithBtn.classList.toggle('active', mode === 'arithmetic');
      modeAlgebraBtn.classList.toggle('active', mode === 'algebra');
      renderTask();
    }

    function renderTask() {
      currentTask = generateTask();
      optionsDiv.innerHTML = '';
      feedbackDiv.textContent = '';
      nextBtn.disabled = true;

      const mathContainer = document.querySelector('#questionBox .math-example');
      mathContainer.innerHTML = '';

      if (currentTask.mode === 'firstLook') {
        const div = document.createElement('div');
        div.className = 'rhythm-display';
        ABCJS.renderAbc(div, currentTask.abc, {
          staffwidth: 150,
          staffheight: 60,
          add_classes: true
        });
        mathContainer.appendChild(div);
      } else {
        const { part1, op, part2, eq, part3 } = currentTask.display;

        const createDiv = (content) => {
          const div = document.createElement('div');
          div.className = 'rhythm-display';
          if (content === '?') {
            div.textContent = '?';
            Object.assign(div.style, {
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              height: '60px',
              minWidth: '60px',
              fontSize: '28px'
            });
          } else {
            ABCJS.renderAbc(div, `L:1/16\nK:perc\n${content}`, {
              staffwidth: 150,
              staffheight: 60,
              add_classes: true
            });
          }
          return div;
        };

        const container1 = createDiv(part1);
        const plus = document.createElement('span');
        plus.textContent = op;
        plus.style.fontWeight = 'bold';
        const container2 = createDiv(part2);
        const equals = document.createElement('span');
        equals.textContent = eq;
        equals.style.fontWeight = 'bold';
        const container3 = createDiv(part3);

        mathContainer.append(container1, plus, container2, equals, container3);
      }

      currentTask.options.forEach((text, i) => {
        const btn = document.createElement('button');
        btn.className = 'option-btn';
        btn.textContent = text;
        btn.onclick = () => selectOption(btn, i);
        optionsDiv.appendChild(btn);
      });
    }

    let selectedOption = null;
    function selectOption(btn, index) {
      document.querySelectorAll('.option-btn').forEach(el => el.classList.remove('selected'));
      btn.classList.add('selected');
      selectedOption = index;
      checkAnswer();
    }

    function checkAnswer() {
      const isCorrect = selectedOption === currentTask.correctIndex;
      feedbackDiv.className = isCorrect ? 'correct' : 'incorrect';
      feedbackDiv.textContent = isCorrect ? '✅ Верно!' : '❌ Неверно. Попробуйте ещё.';

      if (isCorrect) {
        nextBtn.disabled = false;
      } else {
        nextBtn.disabled = true;
      }
    }

    function nextTask() {
      renderTask();
    }

    modeFirstLookBtn.addEventListener('click', () => setMode('firstLook'));
    modeArithBtn.addEventListener('click', () => setMode('arithmetic'));
    modeAlgebraBtn.addEventListener('click', () => setMode('algebra'));
    nextBtn.addEventListener('click', nextTask);
    renderTask();
  </script>
</body>
</html>