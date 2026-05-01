<ul class="{{ $nodes[0]['depth'] ?? 0 > 0 ? 'ml-6 mt-2 border-l-2 border-gray-100 pl-4 dark:border-gray-700' : '' }} space-y-2">
    @foreach($nodes as $node)
        <li>
            <a href="{{ \App\Filament\Resources\StaffDirectoryResource::getUrl('view', ['record' => $node['user']]) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-gray-100 px-3 py-2 text-sm transition hover:border-stripe-purple hover:bg-stripe-purple/5 dark:border-gray-700">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($node['user']->name) }}&color=533afd&background=e5e3ff&size=28"
                     class="h-7 w-7 rounded-full" alt="{{ $node['user']->name }}">
                <div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $node['user']->name }}</span>
                    @if($node['user']->level)
                        <span class="ml-1 text-xs text-gray-400">{{ $node['user']->level->code }}</span>
                    @endif
                </div>
            </a>
            @if(!empty($node['children']))
                @include('filament.partials.org-tree-node', ['nodes' => $node['children']])
            @endif
        </li>
    @endforeach
</ul>
