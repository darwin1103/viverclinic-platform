@props(['appointments', 'title' => '', 'treatments', 'staff'])

<div class="container my-2">
    <div class="row">
        <div class="col-12 d-flex justify-content-center align-items-center p-0">
            <div class="card w-100">
                <div class="card-body m-0">

                    <x-admin.staff.show.appointments-filter :staff="$staff" :treatments="$treatments" />

                    <x-admin.staff.show.appointments-table :appointments="$appointments" />

                </div>
            </div>
        </div>
    </div>
</div>
