<?php

namespace App\Http\Livewire\LifeCycleHook;

use Livewire\Component;
use App\Models\Province;
use Log;

class ProvinceComponent extends Component
{

    public $employees_list = [];
    public $life_cycle_info = [
        'hook' => "",
        'message' => "",
    ];

    public $employee_name = '';
    public $designation = '';
    public $salary = '';

    protected $listeners = [
        'load_employees' => 'loadEmployees',
        'remove_employee' => 'removeEmployee',
    ];

    public $component_id = null;

    public $add_new_row = false;

    public $new_province_form = [
        'id' => '',
        'name' => '',

    ];

    public function mount()
    {
        $this->component_id = $this->id;

        $this->life_cycle_info['hook'] = "mount";
        $this->life_cycle_info['message'] = "Application is mounting...";


    }

    public function hydrate()
    {

        $this->emit('toastr-info', "Application is hydrating...");
        $this->emit('toastr-info', "Loading Employees...");

        $this->life_cycle_info['hook'] = "hydrate";
        $this->life_cycle_info['message'] = "Application is hydrating...";

    }

    public function saveForm()
    {
        try {
            Province::create($this->new_province_form);

            foreach ($this->new_province_form as &$item) {
                $item = '';
            }

            $this->emit('toastr-success', "New Employee created.");
            $this->add_new_row = false;

            $this->emit('load_employees');

        } catch (\Throwable $th) {
            $this->emit('toastr-error', "Something went wrong.");
        }
    }

    public function loadEmployees()
    {
        $this->employees_list = Province::orderBy('id', 'DESC')->get();
        $this->emit('toastr-success', "Loaded Employees.");
    }

    public function prepareForInlineEdit($key, $id, $field)
    {
        $value = $this->employees_list[$key][$field];
        $this->employees_list[$key][$field] = '<input data-id=' . $id . ' data-key=' . $key . ' data-field=' . $field . ' type="text" wire:blur="$emit(' . 'field_value_changed' . ')" value="' . $value . '" > ';
    }

    public function removeEmployee($key)
    {
        $this->employees_list[$key]->delete();
        unset($this->employees_list[$key]);

        $this->emit('toastr-success', "Employee removed.");
    }

    public function updatingEmployeeName($value)
    {

        $this->emit('toastr-info', "Updating Employee Name.");

        $this->life_cycle_info['hook'] = "Updating";
        $this->life_cycle_info['message'] = "Employee Name";

        Log::info("Updating Employee Name");
    }

    public function updatedEmployeeName($value)
    {
        $this->emit('toastr-success', "Updated Employee Name.");

        $this->life_cycle_info['hook'] = "Updated";
        $this->life_cycle_info['message'] = "Employee Name";

        $this->employees_list[$value['key']]->employee_name = $value['value'];
        $this->employees_list[$value['key']]->save();

        Log::info("Updated Employee Name");
    }

    public function updatingDesignation($value)
    {
        $this->emit('toastr-info', "Updating Designation.");

        $this->life_cycle_info['hook'] = "Updating";
        $this->life_cycle_info['message'] = "Designation";

        Log::info("Updating Designation");
    }

    public function updatedDesignation($value)
    {
        $this->emit('toastr-success', "Updated Designation.");

        $this->life_cycle_info['hook'] = "Updated";
        $this->life_cycle_info['message'] = "Designation";

        $this->employees_list[$value['key']]->designation = $value['value'];
        $this->employees_list[$value['key']]->save();

        Log::info("Updated Designation");
    }


    public function updatingSalary($value)
    {
        $this->emit('toastr-info', "Updating Salary.");

        $this->life_cycle_info['hook'] = "Updating";
        $this->life_cycle_info['message'] = "Salary";

        Log::info("Updating Salary");
    }

    public function updatedSalary($value)
    {
        $this->emit('toastr-success', "Updated Salary.");

        $this->life_cycle_info['hook'] = "Updated";
        $this->life_cycle_info['message'] = "Salary";

        $this->employees_list[$value['key']]->salary = $value['value'];
        $this->employees_list[$value['key']]->save();

        Log::info("Updated Salary");
    }


















    public function render()
    {
        return view('livewire.life-cycle-hook.province-component');
    }
}