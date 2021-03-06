<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UserPermission;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;

class UserPermissions extends Component
{
    /**
     * livewire pagination trait.
     */
    use WithPagination;

    public array $state = [
        'data' => [
            'id'     => null,
            'role'   => null,
            'route' => null,
        ],
        'ui'   => [
            'isModalUp' => false,
        ],
    ];

    /**
     * form validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'state.data.route' => [
                'required',
                Rule::in(UserPermission::rolesRouteNamesList()),
            ],
            'state.data.role'  => [
                'required',
                Rule::in(['admin', 'user']),
            ],
        ];
    }

    /**
     * pop the form modal up.
     *
     * @return void
     */
    private function showModal(): void
    {
        $this->state['ui']['isModalUp'] = true;
    }

    /**
     * reset component to its default state.
     *
     * @return void
     */
    private function resetState(): void
    {
        \array_walk_recursive(
            array: $this->state,
            callback: fn (&$val) => $val = \gettype($val) === "boolean" ? false : null
        );
    }

    /**
     * parse model instance's data into state.
     *
     * @param int $id
     * @return void
     */
    private function parseModelDataIntoState(int $id): void
    {
        $modelInstance = UserPermission::findOrFail($id);

        $this->state['data']['id']     = $modelInstance->id;
        $this->state['data']['role']   = $modelInstance->role;
        $this->state['data']['route'] = $modelInstance->route_name;
    }

    /**
     * Listen to emitted events
     *
     * @var array
     */
    protected $listeners = [
        'confirmed',
        'cancelled',
    ];

    /**
     * Show Delete Modal.
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->state['data']['id'] = $id;
        $this->confirm(
            'Are you sure deleting this userpermission? ????',
            [
                'toast'             => false,
                'position'          => 'center',
                'showConfirmButton' => true,
                'cancelButtonText'  => 'No',
                'onConfirmed'       => 'confirmed',
                'onCancelled'       => 'cancelled',
            ]
        );
    }

    /**
     * confirm delete model instance listener.
     *
     * @return void
     */
    public function confirmed(): void
    {
        UserPermission::findOrFail($this->state['data']['id'])->delete();

        $this->resetPage(); // back to pagination first page

        $this->alert(
            'success',
            'UserPermission deleted successfully ????'
        );
    }

    /**
     * cancel delete model instance listener.
     *
     * @return void
     */
    public function cancelled(): void
    {
        $this->alert('info', 'Understood');
    }

    /**
     * handle edit form model
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id)
    {
        $this->resetState();
        $this->resetErrorBag();
        $this->showModal();
        $this->parseModelDataIntoState($id);
    }

    /**
     * pop the form modal up.
     *
     * @return void
     */
    public function create(): void
    {
        $this->resetState();
        $this->resetErrorBag();
        $this->showModal();
    }

    /**
     * reset component state on cancel.
     *
     * @return void
     */
    public function cancel(): void
    {
        $this->resetErrorBag();
        $this->resetState();
    }

    /**
     * form validation attribute names.
     *
     * @return array
     */
    protected function validationAttributes(): array
    {
        return [
            'state.data.route' => 'route',
            'state.data.role'   => 'role',
        ];
    }

    /**
     * get valid key value data for db operations.
     *
     * @return array
     */
    private function getValidData(): array
    {
        $valid = $this->validate();
        $data = $valid['state']['data'];

        return [
            "role"       => $data['role'],
            "route_name" => $data['route'],
        ];
    }

    /**
     * overwrite component's state with the given data.
     *
     * @param array $currentState
     * @return void
     */
    private function overwriteState(array $currentState): void
    {
        \array_walk(
            array: $this->state,
            callback: fn (&$val, $key, array $arg) => $val = $arg[$key],
            arg: $currentState
        );
    }

    /**
     * update model model instance.
     *
     * @param array $data
     * @param array $currentState
     * @return void
     */
    private function update(array $data, array $currentState): void
    {
        try {
            $modelInstance = UserPermission::findOrFail($this->state['data']['id']);
            $modelInstance->update($data);
        } catch (\Throwable $e) {
            $this->overwriteState($currentState);
            $this->alert(
                'error',
                'Oops, Something went wrong',
                config('livewire-alert.alert')
            );
        }
    }

    /**
     * store new model instance.
     *
     * @param array $data
     * @param array $currentState
     * @return void
     */
    private function store(array $data, array $currentState): void
    {
        try {
            UserPermission::create($data);
        } catch (\Throwable $e) {
            $this->overwriteState($currentState);
            $this->alert(
                'error',
                'Oops, Something went wrong',
                config('livewire-alert.alert')
            );
        }
    }

    /**
     * reset component state and show success alert.
     *
     * @return void
     */
    private function handleUIEffects(): void
    {
        $this->resetState();
        $this->alert(
            'success',
            'Submitted successfully!',
            config('livewire-alert.success')
        );
    }

    /**
     * handle UI effects and model model instance.
     *
     * @return void
     */
    public function save(): void
    {
        $data = $this->getValidData();
        $currentState = $this->state;

        (!$this->state['data']['id'])
            ? $this->store($data, $currentState)
            : $this->update($data, $currentState);

        $this->handleUIEffects();
    }

    /**
     * render component presentational UI.
     *
     * @return View|Factory
     */
    public function render(): View|Factory
    {
        return view('livewire.user-permissions', [
            'userPermissions' => UserPermission::paginate(),
            'roles'           => User::rolesList(),
            'routes'          => UserPermission::rolesRouteNamesList(),
        ]);
    }
}
