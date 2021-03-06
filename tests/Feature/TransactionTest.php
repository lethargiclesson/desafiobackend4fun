<?php

namespace Tests\Feature;

use Carbon\Factory;
use Tests\TestCase;
use App\Models\User;
use App\Models\Balance;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * 
 * 
 * endpoint testing
 * 
 */
class TransactionTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutMiddleware;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_transaction()
    {
        $payer = User::factory(4)->create([
            'tipo' => 'usuario',
            'cnpj' => NULL,
        ]);

        $userBalance = Balance::factory()->create([
            'user_id' => $this->faker->randomElement($payer->pluck('id')->toArray()),
            'balance' => $this->faker->randomNumber(4),
        ]);

        $payee = User::factory()->create([
            'tipo' => 'lojista',
            'cnpj' => intval($this->faker->unique(true, 3)->randomNumber(7) . $this->faker->unique(true, 4)->randomNumber(7)),
        ]);

        $sellerBalance = Balance::factory()->create([
            'user_id' => $payee->id,
            'balance' => $this->faker->randomNumber(4),
        ]);

        $value = $this->faker->randomNumber(3);

        $response = $this->postJson('api/transfer', [
            'payer' => $userBalance->user_id,
            'payee' => $payee->id,
            'value' => $value,
        ])->assertStatus(200)->assertExactJson(['success' => 'Transação realizada com sucesso!']);
    }

    public function test_transaction_validation_payer()
    {
        $payer = User::factory(4)->create([
            'tipo' => 'usuario',
            'cnpj' => NULL,
        ]);

        $payee = User::factory()->create([
            'tipo' => 'lojista',
            'cnpj' => intval($this->faker->unique(true, 3)->randomNumber(7) . $this->faker->unique(true, 4)->randomNumber(7)),
        ]);

        $balance = Balance::factory()->create(['user_id' => $this->faker->randomElement($payer->pluck('id')->toArray())]);

        $value = $this->faker->randomNumber(2);

        if ($balance->balance >= $value) {
            $response = $this->postJson('api/transfer', [
                'payee' => $payee->id,
                'value' => $value,
            ])->assertStatus(400)->assertExactJson(['error' => ['payer' => ['The payer field is required.']]]);
        }
    }

    public function test_transaction_validation_payee()
    {
        $payer = User::factory(4)->create([
            'tipo' => 'usuario',
            'cnpj' => NULL,
        ]);

        $payee = User::factory()->create([
            'tipo' => 'lojista',
            'cnpj' => intval($this->faker->unique(true, 3)->randomNumber(7) . $this->faker->unique(true, 4)->randomNumber(7)),
        ]);

        $balance = Balance::factory()->create(['user_id' => $this->faker->randomElement($payer->pluck('id')->toArray())]);

        $value = $this->faker->randomNumber(2);

        if ($balance->balance >= $value) {
            $response = $this->postJson('api/transfer', [
                'payer' => $balance->user_id,
                'value' => $value,
            ])->assertStatus(400)->assertExactJson(['error' => ['payee' => ['The payee field is required.']]]);
        }
    }

    public function test_transaction_validation_value()
    {
        $payer = User::factory(4)->create([
            'tipo' => 'usuario',
            'cnpj' => NULL,
        ]);

        $payee = User::factory()->create([
            'tipo' => 'lojista',
            'cnpj' => intval($this->faker->unique(true, 3)->randomNumber(7) . $this->faker->unique(true, 4)->randomNumber(7)),
        ]);

        $balance = Balance::factory()->create(['user_id' => $this->faker->randomElement($payer->pluck('id')->toArray())]);

        $value = $this->faker->randomNumber(2);

        if ($balance->balance >= $value) {
            $response = $this->postJson('api/transfer', [
                'payer' => $balance->user_id,
                'payee' => $payee->id,
            ])->assertStatus(400)->assertExactJson(['error' => ['value' => ['The value field is required.']]]);
        }
    }

    // public function test_transaction_negative_balance()
    // {

    //     $payeeBalance =
    //         $payee = User::create([
    //             'nome' => $this->faker->firstName() . $this->faker->firstName(),
    //             'cpf' => intval($this->faker->unique()->randomNumber(5) . $this->faker->unique()->randomNumber(6)),
    //             'cnpj' => intval($this->faker->unique()->randomNumber(7) . $this->faker->unique()->randomNumber(7)),
    //             'tipo' => 'lojista',
    //             'email' => $this->faker->unique()->safeEmail,
    //             'email_verified_at' => now(),
    //             'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    //             'remember_token' => Str::random(10),
    //         ]);

    //     $payee->balance()->create(['balance' => $this->faker->randomNumber(3)]);

    //     $payer = User::create([
    //         'nome' => $this->faker->firstName(),
    //         'cpf' => intval($this->faker->unique()->randomNumber(5) . $this->faker->unique()->randomNumber(6)),
    //         'cnpj' => NULL,
    //         'tipo' => 'usuario',
    //         'email' => $this->faker->unique()->safeEmail,
    //         'email_verified_at' => now(),
    //         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    //         'remember_token' => Str::random(10),
    //     ]);

    //     $payer->balance()->create(['balance' => $this->faker->randomNumber(3)]);

    //     $balance = Balance::where('user_id', $payee->id)->first()->balance;

    //     $value = $this->faker->randomNumber(6);

    //     if ($value > $balance) {
    //         $response = $this->postJson('api/transfer', [
    //             'payer' => $payer->id,
    //             'payee' => $payee->id,
    //             'value' => $value,
    //         ])->assertStatus(406)->assertExactJson(['error' => 'Creditos insuficientes.']);
    //     }
    // }

    public function test_transaction_negative_balance2()
    {

        $payer = User::factory(4)->create([
            'tipo' => 'usuario',
            'cnpj' => NULL,
        ]);

        $payee = User::factory()->create([
            'tipo' => 'lojista',
            'cnpj' => intval($this->faker->unique(true, 3)->randomNumber(7) . $this->faker->unique(true, 4)->randomNumber(7)),
        ]);

        $balance = Balance::factory()->create(['user_id' => $this->faker->randomElement($payer->pluck('id')->toArray())]);

        $value = $this->faker->randomNumber(6);

        if ($value > $balance->balance) {
            $response = $this->postJson('api/transfer', [
                'payer' => $balance->user_id,
                'payee' => $payee->id,
                'value' => $value,
            ])->assertStatus(406)->assertExactJson(['error' => 'Creditos insuficientes.']);
        }
    }
}
