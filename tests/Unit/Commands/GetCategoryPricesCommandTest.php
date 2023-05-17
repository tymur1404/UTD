<?php
namespace Tests\Unit\Commands;

use App\Commands\GetCategoryPricesCommand;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

class GetCategoryPricesCommandTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // Устанавливаем базовый путь приложения
        $app['path.base'] = __DIR__.'/../../';
    }

    public function testHandle()
    {
        // Создаем заглушку для Http класса
        $httpMock = Http::fake([
            '*' => Http::response([
                ['category' => 'Electronics', 'price' => 10],
                ['category' => 'Electronics', 'price' => 20],
                ['category' => 'Clothing', 'price' => 15],
                ['category' => 'Clothing', 'price' => 25],
            ]),
        ]);

        // Регистрируем экземпляр заглушки Http в контейнере приложения
        App::instance(Http::class, $httpMock);

        // Захватываем вывод в буфер
        ob_start();

        // Создаем экземпляр команды и вызываем метод handle
        $this->artisan('get:category-prices');

        // Получаем вывод из буфера
        $output = ob_get_clean();

        // Проверяем, что вывод соответствует ожидаемому результату
        $expectedOutput = "Electronics - 30\nClothing - 40\n";
        $this->assertSame($expectedOutput, $output);

        // Проверяем, что запрос к API был выполнен
        $httpMock->assertSent(function ($request) {
            return $request->url() === 'https://fakestoreapi.com/products';
        });
    }
}
