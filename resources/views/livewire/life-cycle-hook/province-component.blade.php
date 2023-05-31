<div>




    <div class="container">
        <h4>Employees List</h4>

        @if (!empty($life_cycle_info['hook']))
            <div class="alert alert-info">
                <h4><strong>{{ $life_cycle_info['hook'] }}</strong>: {{ $life_cycle_info['message'] }}</h4>
            </div>
        @endif

        <button type="button" class="btn btn-primary" style="margin-bottom: 5px;"
            wire:click="$set('add_new_row', true )">Add
            New Employee</button>

        <table class="table table-bordered table-compact">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Kode Provinsi</th>
                    <th>Nama Provinsi</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                @if ($add_new_row)
                    <tr>
                        <td>#</td>
                        <td> <input type="text" wire:model="new_province_form.id" value=""> </td>
                        <td> <input type="text" wire:model="new_province_form.name" value=""> </td>
                        <td>
                            <button type="button" wire:click="saveForm()" class="btn btn-success">Save</button>
                            <button type="button" wire:click="$set('add_new_row', false )"
                                class="btn btn-danger">Remove</button>
                        </td>
                    </tr>
                @endif



                @if (!empty($employees_list))
                    @foreach ($employees_list as $key => $employee)
                        <tr wire:key="{{ $loop->index }}">
                            <td>{{ $loop->iteration }}</td>
                            <td> <span
                                    wire:click="prepareForInlineEdit( {{ $key }}, {{ $employee->id }}, 'employee_name' )"><?php echo $employee->id; ?></span>
                            </td>
                            <td> <span
                                    wire:click="prepareForInlineEdit( {{ $key }}, {{ $employee->id }}, 'designation' )"><?php echo $employee->name; ?></span>
                            </td>

                            <td>
                                <button type="button"
                                    wire:click="$emit('confirm_remove_employee', {{ $key }} )"
                                    class="btn btn-danger">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script type="text/javascript">
            document.addEventListener("livewire:load", function(event) {
                window.livewire.emit('load_employees');


                window.livewire.hook('beforeDomUpdate', () => {
                    window.livewire.emit('toastr-info', "DOM Updating....");
                });

                window.livewire.hook('afterDomUpdate', () => {
                    window.livewire.emit('toastr-info', "DOM Updated.");
                });

            });

            window.livewire.on('field_value_changed', function() {

                let component = window.livewire.find('{{ $component_id }}');


                let element = event.target;
                let value = element.value;
                let key = element.dataset['key'];
                let id = element.dataset['id'];
                let field = element.dataset['field'];

                component.set(field, {
                    'key': key,
                    'id': id,
                    "value": value
                });
            });

            window.livewire.on('confirm_remove_employee', key => {

                let cfn = confirm("Confirm to remove ?");

                if (cfn) {
                    window.livewire.emit('remove_employee', key)
                }

            });


            window.livewire.on('toastr-success', message => toastr.success(message));

            window.livewire.on('toastr-info', message => toastr.info(message));

            window.livewire.on('toastr-error', message => toastr.error(message));
        </script>
    @endpush







</div>
