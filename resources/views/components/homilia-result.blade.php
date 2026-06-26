@props(['message'])

<div class="mb-4 text-right">
    <form action="{{ route('gemini-homilia.export-pdf') }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="sermon_content" value="{{ trim($message) }}">
        <button type="submit"
           class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-full font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15.55 6.22l-4.44-4.44A1 1 0 0010.41 1H5a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V7.59a1 1 0 00-.29-.77zM11 5.41L13.59 8H11V5.41zM15 17H5V3h4v4a1 1 0 001 1h4v9z"></path></svg>
            Exportar para PDF
        </button>
    </form>
</div>
<div class="prose prose-sm prose-compact max-w-full dark:prose-invert bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-100 p-6 rounded-lg shadow-md break-words whitespace-pre-wrap transition-colors duration-300">
    @markdown
         {!! trim($message) !!}
    @endmarkdown
</div>