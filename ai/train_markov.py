import json
import random
from collections import defaultdict
import re

def train_markov_model():
    print("Загружаем чистый датасет V5...")
    with open('dataset_v5.json', 'r', encoding='utf-8') as f:
        dataset = json.load(f)
    
    transitions = defaultdict(lambda: defaultdict(int))
    
    print("Анализируем последовательности тактов...")
    for item in dataset:
        tokens = item['tokens']
        # Оставляем только музыкальные такты
        measures = [t for t in tokens if t not in ['|', '|:', ':|', '||', ':|:']]
        
        if len(measures) < 2:
            continue
        
        # Строим цепь Маркова
        for i in range(len(measures) - 1):
            current = measures[i]
            next_m = measures[i + 1]
            transitions[current][next_m] += 1
    
    model_data = {"transitions": {}}
    for current, next_dict in transitions.items():
        model_data["transitions"][current] = dict(next_dict)
    
    with open('markov_model_final.json', 'w', encoding='utf-8') as f:
        json.dump(model_data, f, ensure_ascii=False, indent=2)
    
    print(f"\n✅ Финальная модель обучена!")
    print(f"Уникальных тактов: {len(model_data['transitions'])}")
    
    print("\n--- ТЕСТОВАЯ ГЕНЕРАЦИЯ (4 такта) ---")
    test_generate(model_data, num_measures=4)
    
    print("\n--- ТЕСТОВАЯ ГЕНЕРАЦИЯ (8 тактов) ---")
    test_generate(model_data, num_measures=8)

def format_measure_visually(measure_str):
    """Умная группировка: c c c c -> cccc, c2 c2 -> c2c2"""
    notes = measure_str.split()
    grouped = []
    i = 0
    while i < len(notes):
        # 1. Группировка шестнадцатых (4 одиночных символа подряд: c c c c -> cccc)
        if i + 3 < len(notes):
            n1, n2, n3, n4 = notes[i], notes[i+1], notes[i+2], notes[i+3]
            if all(len(n) == 1 and re.match(r'^[A-Ga-gZz]$', n) for n in [n1, n2, n3, n4]):
                grouped.append(n1 + n2 + n3 + n4)
                i += 4
                continue
        
        # 2. Группировка восьмых (2 символа длиной 2 подряд: c2 c2 -> c2c2)
        if i + 1 < len(notes):
            n1, n2 = notes[i], notes[i+1]
            if (len(n1) == 2 and len(n2) == 2 and 
                re.match(r'^[A-Ga-gZz][0-9]$', n1) and 
                re.match(r'^[A-Ga-gZz][0-9]$', n2)):
                grouped.append(n1 + n2)
                i += 2
                continue
        
        # 3. Если не подошло ни одно правило, оставляем как есть
        grouped.append(notes[i])
        i += 1
        
    return " ".join(grouped)

def test_generate(model_data, num_measures=8):
    transitions = model_data["transitions"]
    if not transitions:
        return
    
    # Исключаем полные паузы из старта
    good_starts = [m for m in transitions.keys() if m.strip() != 'z16' and len(m) > 2]
    current_measure = random.choice(good_starts)
    result = [current_measure]
    
    for _ in range(num_measures - 1):
        if current_measure in transitions:
            next_options = transitions[current_measure]
            measures_list = list(next_options.keys())
            weights = list(next_options.values())
            next_measure = random.choices(measures_list, weights=weights, k=1)[0]
            result.append(next_measure)
            current_measure = next_measure
        else:
            current_measure = random.choice(good_starts)
            result.append(current_measure)
    
    # Форматируем и выводим
    formatted_measures = [format_measure_visually(m) for m in result]
    
    output_lines = []
    for i in range(0, len(formatted_measures), 4):
        chunk = formatted_measures[i:i+4]
        output_lines.append(" | ".join(chunk))
    
    print("K:C clef=perc\nM:4/4\nL:1/16\n|:\n" + "\n".join(output_lines) + "\n:|")

if __name__ == '__main__':
    train_markov_model()
