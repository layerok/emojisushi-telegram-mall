<?php

return [
    'plugin' => [
        'name' => 'Бот telegram магазину',
        'description' => 'Дозволь своїм покупцям здійснювати замовлення в своєму улюбленому меседжері'
    ],
    'telegram' => [
        'buttons' => [
            "plus" => "➕",
            "minus" => "➖",
            "del" => "❌",
            "load_more" => "Завантажити ще",
            "added_to_cart" => "✅ В кошику",
            "categories" => "🍱 Меню",
            "cart" => "🛒 Кошик",
            "add_to_cart" => "Додати в кошик",
            "in_menu_main" => "🏠 На головну",
            "chose_branch" => "Виберіть заклад",
            "contact" => "☎ Контакти",
            "take_order" => "✅ Оформити замовлення",
            "to_categories" => "🔙 В меню",
            "cancel" => "Відмінити",
            "price" => "Ціна",
            "no" => "Ні",
            "yes" => "Так",
            "next" => "Далі"
        ],

        'texts' => [
            "welcome" => "%s, Ласкаво просимо в наш чат-бот!\n\nЩоб зробити замовлення, натисніть будь ласка на 🍱 Меню.",
            "category" => "Виберіть будь ласка страви які ви хочете замовити",
            "cart_is_empty" => "Ваш кошик порожній.",
            "all_amount_order" => "🧾 Сума замовлення: :price",
            "triple_dot" => "Нижче ви можете перейти до корзини або повернутися на головну",
            "thank_you" => "Дякуємо за Ваше замовлення, ми зв'яжемося з Вами найближчим часом!",
            "new_order" => "Нове замовлення",
            "cart" => "⬇️ Нижче ви можете переглянути суму замовлення, оформити замовлення або повернутися на головну",
            "payment_change" =>  "Підготувати решту з",
            "chose_delivery_method" => "Виберіть тип доставки",
            "chose_payment_method" => "Виберіть тип оплати",
            "right_phone_number" => "Вірний номер телефону",
            "prepare_change_question" => "Бажаєте, щоб ми підготували решту?",
            "leave_comment_question" => "Бажаєте залишити коментар?",
            "add_sticks_question" => "Бажаєте додати палички до замовлення?",
            "type_delivery_address" => "Введіть адресу доставки",
            "type_your_name" => "Введіть Ваше ім'я",
            "type_your_phone" => "Введіть Ваш телефон в форматі +380xxxxxxxxx",
            "add_sticks" => "Додайте бажану кількість паличок",
            "try_again" => "Спробуйте знову"
        ],

        'receipt' => [
            'new_order' => 'Нове замовлення',
            'first_name' => "Ім'я",
            'last_name' => 'Прізвище',
            'phone' => 'Тел',
            'email' => 'Пошта',
            'comment' => 'Коментар',
            'address' => 'Адреса доставки',
            'products' => 'Товари',
            'total'   => 'Разом',
            'delivery_method_name'   => 'Доставка',
            'change'   => 'Підготувати решту з',
            'payment_method_name'   => 'Оплата',
            'payment_status'   => 'Статус оплати',
            'spot' => 'Заклад',
            'order_items' => 'Замовлені товари',
            'delivery_method' => 'Спосіб доставки',
            'payment_method' => 'Спосіб оплаты',
            'sticks_name' => 'Палички для суші',
            'target' => 'Джерело замовлення',
            'site' => 'сайт',
            'bot' => 'чат-бот',
            "confirm_order_question" => "Підтверджуєте замовлення?",
        ],

        'spots' => [
            'choose' => 'Оберіть заклад',
            'change' => '👋 Змінити заклад',
            'changed' => 'Ви обрали заклад'
        ],
        'maintenance_msg' => "Просимо вибачення. Над ботом тимчасово ведуться технічні роботи. Поки що Ви можете скористатися нашим сайтом https://emojisushi.com.ua"
    ],

];
