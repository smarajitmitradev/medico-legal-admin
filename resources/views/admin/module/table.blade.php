@if($modules->count() > 0)
@foreach($modules as $module)
<tr>
    <td>{{ $module->title }}</td>
    <td>
        <div style="
        max-width:300px;
        max-height:120px;
        overflow-y:auto;
        overflow-x:hidden;
        padding:8px;
        background:#f8f9fa;
        border-radius:6px;
        font-size:13px;
        line-height:1.4;
    ">
            {!! $module->description_html !!}
        </div>
    </td>

    <td>
        @php
        $video = trim($module->youtube_link ?? '');
        $youtubeId = null;
        $isShort = false; // ✅ ALWAYS define first

        // youtube shorts
        if (preg_match('/youtube\.com\/shorts\/([^\&\?\/]+)/', $video, $match)) {
        $youtubeId = $match[1];
        $isShort = true;
        }
        // embed
        elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/"]+)/', $video, $match)) {
        $youtubeId = $match[1];
        }
        // watch?v=
        elseif (preg_match('/watch\?v=([^\&\?\/]+)/', $video, $match)) {
        $youtubeId = $match[1];
        }
        // youtu.be
        elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $video, $match)) {
        $youtubeId = $match[1];
        }
        @endphp

        @if($youtubeId)
        <div style="width:200px; height:120px; overflow:hidden; border-radius:8px;">
            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" style="width:100%; height:100%; object-fit:cover;" frameborder="0" allowfullscreen>
            </iframe>
        </div>
        @else
        <a href="{{ $video }}" target="_blank">Watch Video</a>
        @endif
    </td>

    <td>
        @if($module->pdf_file)
        <div style="
            width:200px;
            height:120px;
            overflow:hidden;
            border-radius:8px;
            border:1px solid #ddd;
        ">
            <iframe src="{{ asset('storage/'.$module->pdf_file) }}" style="width:100%; height:100%;" frameborder="0">
            </iframe>
        </div>
        @else
        <span class="text-muted">No File</span>
        @endif
    </td>

    <td>
        <a href="{{ route('module.edit', [$sub->slug, $module->id]) }}" class="btn btn-sm btn-warning">
            <i class="fas fa-edit"></i>
        </a>

        <form action="{{ route('module.destroy', [$sub->slug, $module->id]) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Delete?')" class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="5" class="text-center text-muted">
        No modules found
    </td>
</tr>
@endif