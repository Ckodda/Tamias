<?php

namespace Tests\Feature\Actions\CostCenter;

use App\Actions\CostCenter\CreateCostCenterAction;
use App\Http\Requests\CostCenter\CreateCostCenterRequest;
use App\Http\Responses\CostCenter\CostCenterResponse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateCostCenterActionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_executes_the_creation_process_correctly()
    {
        $data = [
            'CodeCostCenter' => 'T-' . substr(uniqid(), -10),
            'CenterName' => 'Departamento de Pruebas'
        ];
        $request = new CreateCostCenterRequest(...$data);

        /** @var CreateCostCenterAction $action */
        $action = app(CreateCostCenterAction::class);

        $response = $action->execute($request);

        $this->assertInstanceOf(CostCenterResponse::class, $response);
        $this->assertEquals($data['CodeCostCenter'], $response->CodeCostCenter);

        $this->assertDatabaseHas('CostCenters', [
            'CodeCostCenter' => $data['CodeCostCenter']
        ]);
    }

    public function test_it_fails_when_creating_duplicate_cost_center_code()
    {
        $data = [
            'CodeCostCenter' => 'DUP-001',
            'CenterName' => 'Original'
        ];

        /** @var CreateCostCenterAction $action */
        $action = app(CreateCostCenterAction::class);

        // Creamos el primero
        $action->execute(new CreateCostCenterRequest(...$data));

        $this->expectException(\Exception::class);
        $action->execute(new CreateCostCenterRequest(
            CodeCostCenter: 'DUP-001',
            CenterName: 'Otro'
        ));
    }
}
