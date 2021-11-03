@if (count($errors) > 0)
    <div class="alert alert-danger alert-noborder">
        <button data-dismiss="alert" class="close" type="button"><span>×</span><span class="sr-only">{{ __('global.close') }}</span></button>
        <strong>{{ __('validation.fix_errors') }}</strong>
        <br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
