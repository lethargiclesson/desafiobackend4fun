<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $randomNumber = $this->faker->boolean(50);
        $randomType =  $randomNumber ? 'usuario' : 'lojista';

        if ($randomType === 'lojista') {
            $cnpj = intval($this->faker->unique()->randomNumber(7) . $this->faker->unique()->randomNumber(7));
        }
        if ($randomNumber === true) {
            return [
                'nome' => $this->faker->firstName(),
                'cpf' => intval($this->faker->unique()->randomNumber(5) . $this->faker->unique()->randomNumber(6)),
                'cnpj' => NULL,
                'tipo' => $randomType,
                'email' => $this->faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ];
        } else if ($randomNumber === false) {
            return [
                'nome' => $this->faker->firstName(),
                'cpf' => intval($this->faker->unique()->randomNumber(5) . $this->faker->unique()->randomNumber(6)),
                'cnpj' => $cnpj,
                'tipo' => $randomType,
                'email' => $this->faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ];
        }
    }
}
