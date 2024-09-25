<?php

namespace Database\Factories;

use App\Models\Cosecha;
use App\Models\Qr;
use Illuminate\Database\Eloquent\Factories\Factory;

class QrFactory extends Factory
{
    protected $model = Qr::class;

    public function definition()
    {
        return [
            'cosecha_id' => Cosecha::factory(),
            'qr125' => 'qrcodes/' . $this->faker->uuid . '.jpg',
            'qr250' => 'qrcodes/' . $this->faker->uuid . '.jpg',
            'qr500' => 'qrcodes/' . $this->faker->uuid . '.jpg',
            'uuid125' => implode(',', [$this->faker->uuid, $this->faker->uuid]),
            'uuid250' => implode(',', [$this->faker->uuid, $this->faker->uuid]),
            'uuid500' => implode(',', [$this->faker->uuid, $this->faker->uuid]),
        ];
    }
}
