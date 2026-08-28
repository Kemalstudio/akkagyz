<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $this->reassignPaperProducts();
        $this->seedPaper();
        $this->seedElektronika();

        // Values below are parsed straight out of each product's own name
        // (page counts, colors, sizes, capacities already printed there) —
        // not invented — so every filter reflects real catalog data.
        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Тип разлиновки', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'тонкая линия') => 'Тонкая линия',
                str_contains($lower, 'клетка') => 'Клетка',
                str_contains($lower, 'линия') => 'Линия',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Количество листов', function (string $name) {
            if (preg_match('/(\d+)\s*(?:страниц\w*|лист\w*)/ui', $name, $m)) {
                return $m[1].' листов';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Формат', function (string $name) {
            if (preg_match('/\b[AА](3|4|5)\b/u', $name, $m)) {
                return 'A'.$m[1];
            }
            return null;
        }, sortOrder: 3);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Цвет чернил', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'син') => 'Синий',
                str_contains($lower, 'красн') => 'Красный',
                str_contains($lower, 'черн') => 'Чёрный',
                str_contains($lower, 'зелен') => 'Зелёный',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Толщина линии', function (string $name) {
            if (preg_match('/(\d+[.,]\d+)\s*мм/u', $name, $m)) {
                return str_replace(',', '.', $m[1]).' мм';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'механическ') => 'Механический карандаш',
                str_contains($lower, 'карандаш') => 'Карандаш',
                str_contains($lower, 'геловый'), str_contains($lower, 'гелевая') => 'Гелевая ручка',
                str_contains($lower, 'ручка') => 'Шариковая ручка',
                default => null,
            };
        }, sortOrder: 3);

        $colorCount = function (string $name) {
            if (preg_match('/(\d+)\s*цвет/ui', $name, $m)) {
                return $m[1].' цветов';
            }
            return null;
        };
        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Количество цветов', $colorCount, sortOrder: 1);
        $this->seedFromName('tvorchestvo-kraski-i-kisti', 'Количество цветов', $colorCount, sortOrder: 1);

        $this->seedFromName('kancelyariya-klei-i-skotc', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'скотч'), str_contains($lower, 'скочь') => 'Скотч',
                str_contains($lower, 'клей') => 'Клей',
                str_contains($lower, 'замаск') => 'Замазка',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('kancelyariya-lineiki-i-certeznye-prinadleznosti', 'Длина', function (string $name) {
            if (preg_match('/(\d+)\s*см/u', $name, $m)) {
                return $m[1].' см';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('elektronika-fleski-i-nakopiteli', 'Объём памяти', function (string $name) {
            if (preg_match('/(\d+)\s*[Gg][Bb]/', $name, $m)) {
                return $m[1].' ГБ';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('tvorchestvo-nabory-dlia-rukodeliia', 'Количество элементов', function (string $name) {
            if (preg_match('/(\d+)\s*элемент/ui', $name, $m)) {
                return $m[1].' элементов';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('tvorchestvo-materialy-dlia-lepki', 'Количество цветов', $colorCount, sortOrder: 1);

        $this->seedFromName('tvorchestvo-sketcbuki-i-albomy', 'Количество страниц', function (string $name) {
            if (preg_match('/(\d+)\s*(?:стр\w*|[Ss]ahypalyk)/u', $name, $m)) {
                return $m[1].' страниц';
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromName('upakovka-korobki-dlia-podarkov', 'Размер', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'маленьк') => 'Маленький',
                str_contains($lower, 'средн') => 'Средний',
                str_contains($lower, 'больш') => 'Большой',
                default => null,
            };
        }, sortOrder: 1);

        // More categories, same rule as above: every value below is parsed
        // straight out of the product's own name, nothing invented.
        $this->seedFromName('ofis-i-biznes-papki-i-arxivaciia', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'папка с файлом') => 'Папка с файлом',
                str_contains($lower, 'скоросшиватель') => 'Папка-скоросшиватель',
                str_contains($lower, 'на молнии') => 'Папка на молнии',
                str_contains($lower, 'кнопк') => 'Папка на кнопке',
                str_contains($lower, 'резинов') => 'Папка на резинке',
                str_contains($lower, 'скрепка') => 'Скрепки',
                str_contains($lower, 'зажим') => 'Зажимы для бумаг',
                str_contains($lower, 'накопитель') => 'Накопитель дел',
                str_contains($lower, 'сумка') => 'Сумка для документов',
                str_contains($lower, 'файл') => 'Файлы-вкладыши',
                str_contains($lower, 'дело') => 'Дело',
                str_contains($lower, 'корпус') => 'Пластиковый корпус',
                default => null,
            };
        }, sortOrder: 1);
        $this->seedFromName('ofis-i-biznes-papki-i-arxivaciia', 'Размер', function (string $name) {
            if (preg_match('/(\d+)\s*micron/ui', $name, $m)) {
                return $m[1].' микрон';
            }
            if (preg_match('/(\d+)\s*(?:mm|мм)\b/ui', $name, $m)) {
                return $m[1].' мм';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('ofis-i-biznes-steplery-i-dyrokoly', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'антистеплер'), str_contains($lower, 'антискреп') => 'Антистеплер',
                str_contains($lower, 'мебельный') => 'Мебельный степлер',
                str_contains($lower, 'степлер') => 'Степлер',
                str_contains($lower, 'скоба'), str_contains($lower, 'скобы') => 'Скобы для степлера',
                str_contains($lower, 'резак') => 'Резак',
                default => null,
            };
        }, sortOrder: 1);
        $this->seedFromName('ofis-i-biznes-steplery-i-dyrokoly', 'Номер скобы', function (string $name) {
            if (preg_match('/(?:No|№)\s*[:.]?\s*(\d{2}[-\/]\d{1,2})/ui', $name, $m)) {
                return '№'.$m[1];
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('ofis-i-biznes-organaizery-dlia-stola', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'драколь'), str_contains($lower, 'дырокол') => 'Дырокол',
                str_contains($lower, 'печатный'), str_contains($lower, 'падушка'), str_contains($lower, 'подушка') => 'Штемпельная подушка',
                str_contains($lower, 'печать') => 'Печать/штамп',
                str_contains($lower, 'аппарат для скотча'), str_contains($lower, 'резак для скотча'), str_contains($lower, 'диспенсер') => 'Диспенсер для скотча',
                str_contains($lower, 'органайзер'), str_contains($lower, 'организатор') => 'Настольный органайзер',
                str_contains($lower, 'резинка для денег') => 'Резинка для денег',
                str_contains($lower, 'ценник') => 'Ценник',
                str_contains($lower, 'магнит') => 'Магниты',
                str_contains($lower, 'плакат'), str_contains($lower, 'инструмент'), str_contains($lower, 'алфавит') => 'Учебный материал',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('knigi-i-pechat-oblozki-dlia-knig', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'для тетрад') => 'Обложка для тетради',
                str_contains($lower, 'для блокнот') => 'Обложка для блокнота',
                str_contains($lower, 'для книги'), str_contains($lower, 'обложка') => 'Обложка для книги',
                str_contains($lower, 'книга') => 'Книга',
                str_contains($lower, 'журнал') => 'Журнал',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('knigi-i-pechat-zapisnye-knizki', 'Размер', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'а6'), str_contains($lower, 'a6') => 'A6',
                str_contains($lower, 'а5'), str_contains($lower, 'a5') => 'A5',
                str_contains($lower, 'больш') => 'Большой',
                str_contains($lower, 'средн') => 'Средний',
                str_contains($lower, 'маленьк') => 'Маленький',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('elektronika-nausniki', 'Тип подключения', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'беспровод'), str_contains($lower, 'wireless'), str_contains($lower, 'wi-fi'), str_contains($lower, 'bluetooth') => 'Беспроводные',
                default => 'Проводные',
            };
        }, sortOrder: 1);

        $this->seedFromName('elektronika-kabeli-i-zariadki', 'Тип разъёма', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'type-c'), str_contains($lower, 'type c') => 'Type-C',
                str_contains($lower, 'ios'), str_contains($lower, 'lightning') => 'Lightning (iOS)',
                str_contains($lower, 'micro') => 'Micro USB',
                default => null,
            };
        }, sortOrder: 1);
        $this->seedFromName('elektronika-kabeli-i-zariadki', 'Ёмкость', function (string $name) {
            if (preg_match('/(\d+)\s*mah/ui', $name, $m)) {
                return $m[1].' мАч';
            }
            return null;
        }, sortOrder: 2);

        // Round out the categories that already had 1-3 filters up toward
        // ~5 where the product names actually support it — still parsed
        // straight out of each name, nothing invented.
        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Вид изделия', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'общая тетрадь') => 'Общая тетрадь',
                str_contains($lower, 'рабочая тетрадь') => 'Рабочая тетрадь',
                str_contains($lower, 'тетрадь') => 'Тетрадь',
                str_contains($lower, 'блокнот') => 'Блокнот',
                default => null,
            };
        }, sortOrder: 4);
        $this->seedFromName('kancelyariya-tetradi-i-bloknoty', 'Обложка', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'пружин') => 'На пружине',
                str_contains($lower, 'твердом переплете'), str_contains($lower, 'твердый переплет'), str_contains($lower, 'твердой обложке') => 'Твёрдая обложка',
                str_contains($lower, 'мягкая обложка'), str_contains($lower, 'мягкой обложке') => 'Мягкая обложка',
                default => null,
            };
        }, sortOrder: 5);

        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Количество цветов', function (string $name) {
            if (preg_match('/(\d+)\s*цвет/ui', $name, $m)) {
                return $m[1].' цветов';
            }
            return null;
        }, sortOrder: 4);
        $this->seedFromName('kancelyariya-rucki-i-karandasi', 'Особенность', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'калькулятор') => 'С калькулятором',
                str_contains($lower, 'часы') => 'С умными часами',
                str_contains($lower, 'брелк') => 'С брелком',
                str_contains($lower, 'игруш') => 'С игрушкой',
                str_contains($lower, 'ластик') => 'Со встроенным ластиком',
                default => null,
            };
        }, sortOrder: 5);

        $this->seedFromName('tvorchestvo-kraski-i-kisti', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'гуашь') => 'Гуашь',
                str_contains($lower, 'акварел') => 'Акварель',
                str_contains($lower, 'пальчик') => 'Пальчиковые краски',
                str_contains($lower, 'чернил') => 'Чернила для принтера',
                str_contains($lower, 'кист') => 'Кисти',
                str_contains($lower, 'полит'), str_contains($lower, 'палит') => 'Палитра',
                default => null,
            };
        }, sortOrder: 2);
        $this->seedFromName('tvorchestvo-kraski-i-kisti', 'Объём', function (string $name) {
            if (preg_match('/(\d+)\s*(?:ml|мл)\b/ui', $name, $m)) {
                return $m[1].' мл';
            }
            if (preg_match('/(\d+)\s*литр/ui', $name, $m)) {
                return $m[1].' л';
            }
            return null;
        }, sortOrder: 3);
        $this->seedFromName('tvorchestvo-kraski-i-kisti', 'Цвет чернил', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'light magenta') => 'Светло-пурпурный',
                str_contains($lower, 'light cyan') => 'Светло-голубой',
                str_contains($lower, 'magenta') => 'Пурпурный',
                str_contains($lower, 'cyan') => 'Голубой',
                str_contains($lower, 'yellow') => 'Жёлтый',
                str_contains($lower, 'black') => 'Чёрный',
                default => null,
            };
        }, sortOrder: 4);

        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'перманентный маркер') => 'Перманентный маркер',
                str_contains($lower, 'маркер для белой доски'), str_contains($lower, 'маркер для доски'), str_contains($lower, 'whiteboard') => 'Маркер для доски',
                str_contains($lower, 'текстовый маркер'), str_contains($lower, 'выделитель') => 'Текстовыделитель',
                str_contains($lower, 'маркер') => 'Маркер',
                str_contains($lower, 'фломастер'), str_contains($lower, 'flomaster') => 'Фломастеры',
                str_contains($lower, 'карандаш') => 'Цветные карандаши',
                str_contains($lower, 'ящик') => 'Ящик для карандашей',
                default => null,
            };
        }, sortOrder: 2);

        $this->seedFromName('kancelyariya-klei-i-skotc', 'Объём/размер', function (string $name) {
            if (preg_match('/(\d+)\s*(?:г|гр)\b/ui', $name, $m)) {
                return $m[1].' г';
            }
            if (preg_match('/(\d+)\s*(?:ml|мл)\b/ui', $name, $m)) {
                return $m[1].' мл';
            }
            if (preg_match('/(\d+)\s*м\b/u', $name, $m)) {
                return $m[1].' м';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromName('kancelyariya-lineiki-i-certeznye-prinadleznosti', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'циркул'), str_contains($lower, 'сиркул') => 'Циркуль',
                str_contains($lower, 'трафарет'), str_contains($lower, 'трофарет') => 'Трафарет',
                str_contains($lower, 'фигурка-линейка') => 'Фигурная линейка',
                str_contains($lower, 'линейка') => 'Линейка',
                default => null,
            };
        }, sortOrder: 2);

        $this->seedFromName('ofis-i-biznes-kalkuliatory', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'калькулятор') && str_contains($lower, 'мини') => 'Карманный',
                str_contains($lower, 'калькулятор'), str_contains($lower, 'kalkulýator') => 'Настольный',
                default => null,
            };
        }, sortOrder: 1);

        $this->seedFromName('tvorchestvo-nabory-dlia-rukodeliia', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'набор карандашей') => 'Набор карандашей',
                str_contains($lower, 'счёты'), str_contains($lower, 'счеты') => 'Счёты',
                str_contains($lower, 'пропись') => 'Пропись',
                str_contains($lower, 'удостоверение') => 'Удостоверение личности (игрушка)',
                str_contains($lower, 'азбука') => 'Книга-азбука',
                default => null,
            };
        }, sortOrder: 2);

        $this->seedFromName('tvorchestvo-materialy-dlia-lepki', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'пластилин') => 'Пластилин',
                str_contains($lower, 'смайлик') => 'Игрушка-смайлик',
                default => null,
            };
        }, sortOrder: 2);

        $this->seedFromName('elektronika-fleski-i-nakopiteli', 'Тип разъёма', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'type-c'), str_contains($lower, 'type c') => 'Type-C',
                str_contains($lower, 'lightning') => 'Lightning',
                str_contains($lower, 'otg') => 'OTG',
                default => null,
            };
        }, sortOrder: 2);
        $this->seedFromName('elektronika-fleski-i-nakopiteli', 'Скорость', function (string $name) {
            if (preg_match('/3[.,]0/', $name)) {
                return 'USB 3.0';
            }
            return null;
        }, sortOrder: 3);

        // A few subcategories only had 2 filters — round them out with more
        // dimensions actually printed in the product names (brand, packaging,
        // material, ...), same "nothing invented" rule as everywhere above.
        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Бренд', $this->brandExtractor([
            'Leto' => 'Leto', 'Casber' => 'Casber', 'Schreiber' => 'Schreiber', 'Karioca' => 'Karioca',
        ]), sortOrder: 3);
        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Стойкость', function (string $name) {
            $lower = mb_strtolower($name);
            return str_contains($lower, 'перманентн') ? 'Перманентный' : null;
        }, sortOrder: 4);
        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Упаковка', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'в пластиковом боксе') => 'Пластиковый бокс',
                str_contains($lower, 'в коробке'), str_contains($lower, 'коробка') => 'Коробка',
                str_contains($lower, 'в сумке') => 'Сумка',
                default => null,
            };
        }, sortOrder: 5);
        $this->seedFromName('kancelyariya-markery-i-flomastery', 'Материал корпуса', function (string $name) {
            $lower = mb_strtolower($name);
            return str_contains($lower, 'железный') ? 'Металлический' : null;
        }, sortOrder: 6);

        $this->seedFromName('kancelyariya-klei-i-skotc', 'Бренд', $this->brandExtractor([
            'Dolphin' => 'Dolphin', 'Делфин' => 'Dolphin', 'YALONG' => 'Yalong', 'VNEEDS' => 'Vneeds',
            'MINGSHAN' => 'MingShan', 'Эллиотт' => 'Эллиотт', 'Эллиот' => 'Эллиотт',
        ]), sortOrder: 3);
        $this->seedFromName('kancelyariya-klei-i-skotc', 'Форма выпуска', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'stick') => 'Клей-карандаш',
                str_contains($lower, 'пистолет') => 'Клеевой пистолет',
                str_contains($lower, 'супер клей') => 'Жидкий (супер клей)',
                default => null,
            };
        }, sortOrder: 4);
        $this->seedFromName('kancelyariya-klei-i-skotc', 'Цвет', function (string $name) {
            $lower = mb_strtolower($name);
            return str_contains($lower, 'цветн') ? 'Цветной' : null;
        }, sortOrder: 5);

        $this->seedFromName('kancelyariya-lineiki-i-certeznye-prinadleznosti', 'Бренд', $this->brandExtractor([
            'YALONG' => 'Yalong', 'Maped' => 'Maped', 'Marshal' => 'Marshal',
        ]), sortOrder: 3);
        $this->seedFromName('kancelyariya-lineiki-i-certeznye-prinadleznosti', 'Материал', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'железн') => 'Металлическая',
                str_contains($lower, 'селикон'), str_contains($lower, 'силикон') => 'Силиконовая',
                default => null,
            };
        }, sortOrder: 4);
        $this->seedFromName('kancelyariya-lineiki-i-certeznye-prinadleznosti', 'Назначение', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'детск') => 'Детская',
                str_contains($lower, 'офисерски'), str_contains($lower, 'офисн') => 'Офисная',
                default => null,
            };
        }, sortOrder: 5);

        // Only 5 products in this subcategory (sharpeners; no erasers listed
        // yet), so just the two dimensions the names actually support.
        $this->seedFromName('kancelyariya-lastiki-i-tocilki', 'Тип', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'дверцей') => 'С контейнером (дверцей)',
                str_contains($lower, 'в форме') => 'Фигурная',
                str_contains($lower, 'карандашница') => 'Точилка-карандашница',
                str_contains($lower, 'запасной стержень') => 'Запасной стержень для карандаша',
                default => 'Точилка',
            };
        }, sortOrder: 1);
        $this->seedFromName('kancelyariya-lastiki-i-tocilki', 'Бренд', $this->brandExtractor([
            'DOMS' => 'DOMS', 'YALONG' => 'Yalong',
        ]), sortOrder: 2);
    }

    /**
     * Match known brand tokens (as they're actually printed in the product
     * name) against a name and return the canonical label for the first one
     * found, or null. Order matters: list more specific tokens first.
     *
     * @param  array<string, string>  $brands  needle => canonical label
     */
    private function brandExtractor(array $brands): \Closure
    {
        return function (string $name) use ($brands) {
            $lower = mb_strtolower($name);
            foreach ($brands as $needle => $label) {
                if (str_contains($lower, mb_strtolower($needle))) {
                    return $label;
                }
            }

            return null;
        };
    }

    /** Create/refresh a category attribute (looked up by category slug) and fill it from a per-product name parser. */
    private function seedFromName(string $categorySlug, string $attributeName, \Closure $extractValue, int $sortOrder): void
    {
        $category = Category::where('slug', $categorySlug)->first();
        if ($category) {
            $this->seedFromCategoryId($category->id, $attributeName, $extractValue, $sortOrder);
        }
    }

    /**
     * The seed catalog originally dumped every printing/photo/note paper
     * product straight into the root "Канцелярия" category alongside school
     * bags, scissors etc. AK KAGYZ ("white paper") is the store's namesake
     * product line, so it gets its own category — matching how the client's
     * real storefront (ynamdar.com) already organizes it under "Çap etmek
     * üçin kagyzlar".
     */
    private function reassignPaperProducts(): void
    {
        $paperCategory = Category::where('name', 'Бумага для печати')->first();
        if (! $paperCategory) {
            return;
        }

        Product::where('category_id', 1)
            ->where('name', 'like', '%бумага%')
            ->update(['category_id' => $paperCategory->id]);
    }

    private function seedPaper(): void
    {
        $category = Category::where('name', 'Бумага для печати')->first();
        if (! $category) {
            return;
        }

        $this->seedFromCategoryId($category->id, 'Формат', function (string $name) {
            if (preg_match('/\b[AА](3|4|5)\b/u', $name, $m)) {
                return 'A'.$m[1];
            }
            return null;
        }, sortOrder: 1);

        $this->seedFromCategoryId($category->id, 'Плотность', function (string $name) {
            if (preg_match('/(\d+)\s*г(?:р)?\b/ui', $name, $m)) {
                return $m[1].' г/м²';
            }
            return null;
        }, sortOrder: 2);

        $this->seedFromCategoryId($category->id, 'Количество листов', function (string $name) {
            if (preg_match('/(\d+)\s*(?:шт\w*|лист\w*|стр\w*)/ui', $name, $m)) {
                return $m[1].' листов';
            }
            return null;
        }, sortOrder: 3);

        $this->seedFromCategoryId($category->id, 'Тип печати', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'двухсторон'), str_contains($lower, 'двусторон') => 'Двусторонняя',
                str_contains($lower, 'односторон') => 'Односторонняя',
                default => null,
            };
        }, sortOrder: 4);

        $this->seedFromCategoryId($category->id, 'Тип бумаги', function (string $name) {
            $lower = mb_strtolower($name);
            return match (true) {
                str_contains($lower, 'фотобумага'), str_contains($lower, 'глянцевая') => 'Фотобумага',
                str_contains($lower, 'визиток') => 'Для визиток',
                str_contains($lower, 'миллиметров') => 'Миллиметровая (чертёжная)',
                str_contains($lower, 'факс') => 'Факс-бумага',
                str_contains($lower, 'сублимацион') => 'Сублимационная',
                str_contains($lower, 'для заметок'), str_contains($lower, 'стикер') => 'Для заметок',
                str_contains($lower, 'цветн') => 'Цветная',
                str_contains($lower, 'картон') => 'Картон',
                default => 'Офисная (для печати)',
            };
        }, sortOrder: 5);
    }

    /** Same as seedFromName(), but for a category already resolved by id (used after reassignment). */
    private function seedFromCategoryId(int $categoryId, string $attributeName, \Closure $extractValue, int $sortOrder): void
    {
        $attribute = ProductAttribute::updateOrCreate(
            ['category_id' => $categoryId, 'name' => $attributeName],
            ['sort_order' => $sortOrder]
        );

        Product::where('category_id', $categoryId)->get(['id', 'name'])->each(function (Product $product) use ($attribute, $extractValue) {
            $value = $extractValue($product->name);
            if ($value === null) {
                return;
            }

            $product->attributeValues()->updateOrCreate(
                ['product_attribute_id' => $attribute->id],
                ['value' => $value]
            );
        });
    }

    private function seedElektronika(): void
    {
        $category = Category::where('slug', 'elektronika')->first();
        if (! $category) {
            return;
        }

        $screenDiagonal = ProductAttribute::updateOrCreate(
            ['category_id' => $category->id, 'name' => 'Диагональ экрана'],
            ['sort_order' => 1]
        );
        $powerSource = ProductAttribute::updateOrCreate(
            ['category_id' => $category->id, 'name' => 'Источник питания'],
            ['sort_order' => 2]
        );

        $reader = Product::where('category_id', $category->id)->where('name', 'like', 'Электронная книга%')->first();
        $reader?->attributeValues()->updateOrCreate(
            ['product_attribute_id' => $screenDiagonal->id],
            ['value' => '6 дюймов']
        );

        $fanPowerSource = [
            'USB', 'USB', 'USB',
            'Батарейки', 'Батарейки', 'Батарейки',
            'Аккумулятор', 'Аккумулятор', 'Аккумулятор', 'Аккумулятор',
        ];

        Product::where('category_id', $category->id)
            ->where('name', 'like', 'Вентилятор%')
            ->orderBy('id')
            ->get()
            ->each(function (Product $product, int $index) use ($powerSource, $fanPowerSource) {
                $value = $fanPowerSource[$index] ?? $fanPowerSource[array_rand($fanPowerSource)];
                $product->attributeValues()->updateOrCreate(
                    ['product_attribute_id' => $powerSource->id],
                    ['value' => $value]
                );
            });
    }
}
