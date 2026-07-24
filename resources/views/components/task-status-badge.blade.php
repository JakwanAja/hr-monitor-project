@props(['status'])

@if($status === 'completed')
    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                 text-xs font-medium bg-green-50 text-green-700">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"/>
        </svg>
        Selesai
    </span>
@elseif($status === 'not_done')
    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                 text-xs font-medium bg-red-50 text-red-700">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"/>
        </svg>
        Tidak Dikerjakan
    </span>
@else
    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                 bg-yellow-50 text-yellow-700">
        Belum Selesai
    </span>
@endif