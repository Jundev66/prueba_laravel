<?php

declare(strict_types=1);

namespace App\Modules\Currency\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Currency\Application\SearchAll\CurrenciesSearcher;
use App\Modules\Currency\Application\SearchAll\SearchCurrenciesQuery;
use App\Modules\Currency\Infrastructure\Http\Requests\ListCurrenciesRequest;
use App\Modules\Currency\Infrastructure\Http\Resources\CurrencyResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class ListCurrenciesController extends Controller
{
    public function __construct(
        private readonly CurrenciesSearcher $searcher,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/currencies',
        summary: 'Get list of all supported currencies with exchange rates',
        description: 'Returns the catalogue of currencies (e.g. USD, EUR, VES) and their exchange rates against base currency.',
        tags: ['Currencies'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                description: 'Search keyword matching currency name',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Dollar')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of currencies retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'US Dollar'),
                            new OA\Property(property: 'symbol', type: 'string', example: '$'),
                            new OA\Property(property: 'exchange_rate', type: 'number', format: 'float', example: 1.0),
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Query parameter validation error (e.g. search keyword exceeds 100 characters)'),
        ]
    )]
    public function __invoke(ListCurrenciesRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();
        $query = isset($validated['q']) ? (string) $validated['q'] : null;

        $currencies = ($this->searcher)(new SearchCurrenciesQuery($query));

        return CurrencyResource::collection($currencies);
    }
}
