<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Suv yetkazib berish API",
    description: "Mobil ilovalar uchun suv yetkazib berish tizimining rasmiy API hujjatlari. Barcha endpointlar Uzbek tilida izohlangan bo‘lib, mijoz (user) va xodim (director/courier) ilovalari uchun mo‘ljallangan.",
)]
#[OA\Server(
    url: "/api",
    description: "Asosiy API server (Laravel backend ichidagi /api prefiksi bilan)"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "apiKey",
    in: "header",
    name: "Authorization",
    description: "Sanctum token orqali kirish. Format: \"Bearer {token}\""
)]
class OpenApi
{
}

