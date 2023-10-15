<div class="card" x-data="{ modalTokenIsOpen : false }" @name-modal-personal-access-token-is-open="modalTokenIsOpen = true">
    <div class="card-header p-0">
        <h3 class="card-title">{{ __('CreateTitle', ['name' => __('Personalaccesstoken') ]) }}</h3>
        <div class="px-2 mt-4">
            <ul class="breadcrumb mt-3 py-3 px-4 rounded">
                <li class="breadcrumb-item"><a href="@route(getRouteName().'.home')" class="text-decoration-none">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="@route(getRouteName().'.personalaccesstoken.read')" class="text-decoration-none">{{ __(\Illuminate\Support\Str::plural('Personalaccesstoken')) }}</a></li>
                <li class="breadcrumb-item active">{{ __('Create') }}</li>
            </ul>
        </div>
    </div>

    <form class="form-horizontal" wire:submit.prevent="create" enctype="multipart/form-data">

        <div class="card-body">
            <div class="form-group position-relative">
                <label for='input-name' class='col-sm-2 control-label '> {{ __('Users') }}</label>
                <input id="model" wire:click="setModel" type="text" class="form-control rounded @error('model') is-invalid @enderror" wire:model="model">
                @if($models and $dropdown)
                    <div @click.away="Livewire.emit('closeModal')" class="bg-white position-absolute w-100 mt-2 rounded d-flex flex-column shadow" style="z-index: 10">
                        @foreach($models as $key => $model)
                            <div class="px-3 py-2 autocomplete-item"  wire:click.prevent="setSuggestedModel({{ $key }})">
                                <a href="" class="py-2 ">{{ $model }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
                @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Nombre Input -->
            <div class='form-group'>
                <label for='input-name' class='col-sm-2 control-label '> {{ __('Nombre') }}</label>
                <input type='text' id='input-name' wire:model.lazy='name' class="form-control  @error('name') is-invalid @enderror" placeholder='' autocomplete='on'>
                @error('name') <div class='invalid-feedback'>{{ $message }}</div> @enderror
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info ml-4">{{ __('Create') }}</button>
            <a href="@route(getRouteName().'.personalaccesstoken.read')" class="btn btn-default float-left">{{ __('Cancel') }}</a>
        </div>
    </form>
    <div x-show="modalTokenIsOpen" class="cs-modal animate__animated animate__fadeIn">
        <div class="bg-white shadow rounded p-5" @click.away="modalTokenIsOpen = false">
            <h5 class="pb-2 border-bottom">{{ __('CreateTokenTitle') }}</h5>
            <p>{{ __('CreateTokenMessage') }}</p>
            <p>{{ $token }}</p>
            <div class="mt-5 d-flex justify-content-between">
                <a @click.prevent="modalTokenIsOpen = false" class="text-white btn btn-danger shadow">{{ __('No, Cancel it.') }}</a>
            </div>
        </div>
    </div>
</div>
