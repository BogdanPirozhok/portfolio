<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = json_decode('[
            {"ru":"🇷🇺 Россия","en":"🇷🇺 Russia"},
            {"ru":"🇹🇷 Турция","en":"🇹🇷 Turkey"},
            {"ru":"🇨🇾 Кипр","en":"🇨🇾 Cyprus"},
            {"ru":"🇦🇪 ОАЭ","en":"🇦🇪 United Arab Emirates"},
            {"ru":"🇺🇦 Украина","en":"🇺🇦 Ukraine"},
            {"ru":"🇰🇿 Казахстан","en":"🇰🇿 Kazakhstan"},
            {"ru":"🇬🇪 Грузия","en":"🇬🇪 Georgia"},
            {"ru":"🇮🇩 Индонезия","en":"🇮🇩 Indonesia"},
            {"ru":"🇦🇲 Армения","en":"🇦🇲 Armenia"},
            {"ru":"🇷🇸 Сербия","en":"🇷🇸 Serbia"},
            {"ru":"🇱🇹 Литва","en":"🇱🇹 Lithuania"},
            {"ru":"🇱🇻 Латвия","en":"🇱🇻 Latvia"},
            {"ru":"🇪🇸 Испания","en":"🇪🇸 Spain"},
            {"ru":"🇮🇹 Италия","en":"🇮🇹 Italy"},
            {"ru":"🇨🇳 Китай","en":"🇨🇳 China"},
            {"ru":"🇸🇨 Сейшелы","en":"🇸🇨 Seychelles"},
            {"ru":"🇪🇪 Эстония","en":"🇪🇪 Estonia"},
            {"ru":"🇺🇸 США","en":"🇺🇸 USA"},
            {"ru":"🇨🇦 Канада","en":"🇨🇦 Canada"},
            {"ru":"🇵🇹 Португалия","en":"🇵🇹 Portugal"},
            {"ru":"🇮🇳 Индия","en":"🇮🇳 India"},
            {"ru":"🇹🇭 Тайланд","en":"🇹🇭 Thailand"},
            {"ru":"🇻🇳 Вьетнам","en":"🇻🇳 Vietnam"},
            {"ru":"🇦🇺 Австралия","en":"🇦🇺 Australia"},
            {"ru":"🇦🇹 Австрия","en":"🇦🇹 Austria"},
            {"ru":"🇦🇿 Азербайджан","en":"🇦🇿 Azerbaijan"},
            {"ru":"🇦🇷 Аргентина","en":"🇦🇷 Argentina"},
            {"ru":"🇦🇫 Афганистан","en":"🇦🇫 Afganistan"},
            {"ru":"🇧🇾 Беларусь","en":"🇧🇾 Belarus"},
            {"ru":"🇧🇪 Бельгия","en":"🇧🇪 Belgium"},
            {"ru":"🇧🇬 Болгария","en":"🇧🇬 Bulgaria"},
            {"ru":"🇧🇷 Бразилия","en":"🇧🇷 Brazil"},
            {"ru":"🇬🇧 Великобритания","en":"🇬🇧 Great Britain"},
            {"ru":"🇭🇺 Венгрия","en":"🇭🇺 Hungary"},
            {"ru":"🇻🇪 Венесуэла","en":"🇻🇪 Venezuela"},
            {"ru":"🇩🇪 Германия","en":"🇩🇪 Germany"},
            {"ru":"🇬🇷 Греция","en":"🇬🇷 Greece"},
            {"ru":"🇩🇰 Дания","en":"🇩🇰 Denmark"},
            {"ru":"🇪🇬 Египет","en":"🇪🇬 Egypt"},
            {"ru":"🇮🇱 Израиль","en":"🇮🇱 Israel"},
            {"ru":"🇮🇪 Ирландия","en":"🇮🇪 Ireland"},
            {"ru":"🇨🇺 Куба","en":"🇨🇺 Cuba"},
            {"ru":"🇰🇬 Кыргызстан","en":"🇰🇬 Kyrgyzstan"},
            {"ru":"🇱🇺 Люксембург","en":"🇱🇺 Luxembourg"},
            {"ru":"🇲🇹 Мальта","en":"🇲🇹 Malta"},
            {"ru":"🇲🇽 Мексика","en":"🇲🇽 Mexico"},
            {"ru":"🇲🇩 Молдавия","en":"🇲🇩 Moldavia"},
            {"ru":"🇳🇱 Нидерланды","en":"🇳🇱 Netherlands"},
            {"ru":"🇳🇿 Новая Зеландия","en":"🇳🇿 New Zeland"},
            {"ru":"🇳🇴 Норвегия","en":"🇳🇴 Norway"},
            {"ru":"🇵🇰 Пакистан","en":"🇵🇰 Pakistan"},
            {"ru":"🇵🇱 Польша","en":"🇵🇱 Poland"},
            {"ru":"🇷🇴 Румыния","en":"🇷🇴 Romania"},
            {"ru":"🇸🇦 Саудовская Аравия","en":"🇸🇦 Saudi Arabia"},
            {"ru":"🇸🇬 Сингапур","en":"🇸🇬 Singapore"},
            {"ru":"🇸🇰 Словакия","en":"🇸🇰 Slovakia"},
            {"ru":"🇸🇮 Словения","en":"🇸🇮 Slovenia"},
            {"ru":"🇹🇯 Таджикистан","en":"🇹🇯 Tajikistan"},
            {"ru":"🇹🇳 Тунис","en":"🇹🇳 Tunisia"},
            {"ru":"🇺🇿 Узбекистан","en":"🇺🇿 Uzbekistan"},
            {"ru":"🇵🇭 Филиппины","en":"🇵🇭 Philippines"},
            {"ru":"🇫🇮 Финляндия","en":"🇫🇮 Finland"},
            {"ru":"🇫🇷 Франция","en":"🇫🇷 France"},
            {"ru":"🇭🇷 Хорватия","en":"🇭🇷 Croatia"},
            {"ru":"🇨🇿 Чехия","en":"🇨🇿 Czech Republic"},
            {"ru":"🇨🇭 Швейцария","en":"🇨🇭 Switzerland"},
            {"ru":"🇸🇪 Швеция","en":"🇸🇪 Sweden"},
            {"ru":"🏴󠁧󠁢󠁳󠁣󠁴󠁿 Шотландия","en":"🏴󠁧󠁢󠁳󠁣󠁴󠁿 Scotland"},
            {"ru":"🇿🇦 Южная Африка","en":"🇿🇦 South Africa"},
            {"ru":"🇰🇷 Южная Корея","en":"🇰🇷 South Korea"},
            {"ru":"🇯🇵 Япония","en":"🇯🇵 Japan"}
        ]', true);

        Country::query()->delete();

        foreach ($countries as $country) {
            Country::query()->create([
                'name' => $country
            ]);
        }
    }
}
