import os
import json
import re
from collections import Counter

LIBRARY_PATH = r"C:\drumz\library"

def clean_abc_string(abc_string):
    """Умная очистка ABC: удаляет директивы, динамику, акценты, но оставляет кварты."""
    # 1. Удаляем директивы в скобках [L:1/16], [M:4/4]
    cleaned = re.sub(r'\[\s*[LMKVXPQI]\s*:[^\]]*\]', '', abc_string)
    
    # 2. Удаляем директивы в начале строк
    lines = cleaned.split('\n')
    filtered_lines = []
    for line in lines:
        stripped = line.strip()
        if re.match(r'^[LMKVXPQI]\s*:', stripped, re.IGNORECASE):
            continue
        filtered_lines.append(line)
    cleaned = '\n'.join(filtered_lines)
    
    # 3. Заменяем переносы строк на тактовые черты
    cleaned = re.sub(r'\n+', ' | ', cleaned)
    
    # 4. Удаляем динамику (!pp!, !p!, !f!, !ff!)
    cleaned = re.sub(r'![a-zA-Z]+!', '', cleaned)
    
    # 5. Удаляем акценты (L, M, K перед нотами): Lc4 → c4, Lc2Lc2 → c2c2
    cleaned = re.sub(r'\b[LMK]([A-Ga-gZz])', r'\1', cleaned)
    
    # 6. Разбиваем кварты на отдельные ноты: cccc → c c c c, c2c2c2c2 → c2 c2 c2 c2
    # Ищем последовательности из 4+ одинаковых нот без пробелов
    def expand_quads(match):
        note = match.group(1)
        duration = match.group(2) if match.group(2) else ''
        return ' '.join([note + duration] * 4)
    
    cleaned = re.sub(r'([A-Ga-gZz])([0-9]?)(\1\2){3,}', expand_quads, cleaned)
    
    return cleaned

def tokenize_measures(abc_string):
    """Разбивает ABC на такты, оставляя только валидные ноты и аккорды."""
    cleaned = clean_abc_string(abc_string)
    
    # Разделяем по тактовым чертам
    parts = re.split(r'(\|:|:\|:|:\||\|\||\|)', cleaned)
    
    tokens = []
    current_measure_notes = []
    
    for part in parts:
        part = part.strip()
        if not part:
            continue
            
        if part in ['|', '|:', ':|', ':|:', '||']:
            if current_measure_notes:
                tokens.append(' '.join(current_measure_notes))
                current_measure_notes = []
            tokens.append(part)
        else:
            # Извлекаем ТОЛЬКО валидные ноты и аккорды
            # Ноты: c4, F2, z16, Z2
            # Аккорды: [F2g2], [c4a]
            valid_notes = re.findall(r'\[[^\]]+\]|[A-Ga-gZz][0-9]*', part)
            current_measure_notes.extend(valid_notes)
    
    if current_measure_notes:
        tokens.append(' '.join(current_measure_notes))
    
    return tokens

def collect_dataset():
    dataset = []
    for root, dirs, files in os.walk(LIBRARY_PATH):
        for filename in files:
            if filename.endswith('.json'):
                filepath = os.path.join(root, filename)
                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        data = json.load(f)
                    if 'fragments' in data:
                        for fragment in data['fragments']:
                            if 'abc' in fragment:
                                tokens = tokenize_measures(fragment['abc'])
                                dataset.append({
                                    'file': filename,
                                    'title': data.get('title', 'Unknown'),
                                    'category': data.get('category', []),
                                    'difficulty': data.get('difficulty', '0'),
                                    'tokens': tokens
                                })
                except Exception as e:
                    print(f"Ошибка при чтении {filename}: {e}")
    return dataset

def analyze_dataset(dataset):
    all_tokens = []
    for item in dataset:
        all_tokens.extend(item['tokens'])
    
    unique_tokens = sorted(list(set(all_tokens)))
    token_counts = Counter(all_tokens)
    top_tokens = token_counts.most_common(20)
    
    print(f"\n=== СТАТИСТИКА ДАТАСЕТА (V5 - УМНАЯ ОЧИСТКА) ===")
    print(f"Всего фрагментов: {len(dataset)}")
    print(f"Всего элементов: {len(all_tokens)}")
    print(f"Уникальных элементов: {len(unique_tokens)}")
    print(f"\nТоп-20 тактов:")
    
    measure_top = [(t, c) for t, c in top_tokens if t not in ['|', '|:', ':|', '||', ':|:']]
    for token, count in measure_top[:20]:
        print(f"  {token}: {count} раз")
    
    return unique_tokens

def save_dataset(dataset, unique_tokens):
    with open('dataset_v5.json', 'w', encoding='utf-8') as f:
        json.dump(dataset, f, ensure_ascii=False, indent=2)
    with open('vocab_v5.json', 'w', encoding='utf-8') as f:
        json.dump(unique_tokens, f, ensure_ascii=False, indent=2)
    print(f"\nДатасет сохранен в: dataset_v5.json")

if __name__ == '__main__':
    print("Собираем библиотеку (V5 - умная очистка)...")
    dataset = collect_dataset()
    if dataset:
        unique_tokens = analyze_dataset(dataset)
        save_dataset(dataset, unique_tokens)
        print("\nГотово! Кварты развернуты, акценты и динамика удалены.")
