@section('content')

<ol class="breadcrumb">
    <li><a href="{{ route('.human.index') }}">Гуманоиды</a></li>
    <li><a href="{{ route('.human.view') }}">{{ $human->title }}</a></li>
    <li class="active">{{ $pet->id ? $pet->title : 'Добавление питомца' }}</li>
</ol>

<div class="row">
    <div class="col-md-9">

        <?php $url = $pet->id ? route('.pet.update', $pet->id) : route('.pet.store'); ?>

        {{ Form::model($pet, array('url' => $url, 'class' => 'form-horizontal', 'human' => 'form')) }}
        {{ Form::hidden('human_id', $human->id) }}
        {{ Form::textField('title', 'Название') }}
        {{ Form::textareaField('description', 'Биография') }}
        {{ Form::imageField('image', 'Изображение') }}
        {{ Form::submitField() }}
        {{ Form::close() }}
    </div>
</div>

@endsection