<div class="max-w-4xl mx-auto px-3 px-md-4 py-4 py-md-8">
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            {{-- <h1 class="text-2xl font-bold text-gray-900 px-6 py-4">User T & C</h1> --}}
        </div>

        <div class="px-4 px-md-6 py-3 py-md-4 space-y-4 space-y-md-6">
            <h2 class="text-lg text-md-xl font-semibold mb-3 mb-md-4 text-center">Guest Terms and Conditions</h2>

            <div class="space-y-4 space-y-md-6">
                @foreach ($terms as $term)
                    <div>
                        <h3 class="text-base text-md-lg font-medium mb-2">{{ $term->title }}</h3>
                        <div class="pl-3 pl-md-4">
                            @if (is_array($term->content))
                                @foreach ($term->content as $key => $content)
                                    @if (is_array($content))
                                        <div class="mb-2">
                                            <p class="text-gray-700 font-medium text-sm text-md-base">
                                                {{ $key }})</p>
                                            <div class="pl-3 pl-md-4 space-y-1 space-y-md-2">
                                                @foreach ($content as $subKey => $subContent)
                                                    <p class="text-gray-700 text-sm text-md-base">{{ $subKey }})
                                                        {{ $subContent }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-gray-700 text-sm text-md-base mb-2">{{ $key }})
                                            {{ $content }}</p>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-gray-700 text-sm text-md-base">{{ $term->content }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <style>
        /* Base styles */
        .max-w-4xl {
            max-width: 56rem;
        }

        /* Font sizes */
        .text-lg {
            font-size: 1.125rem;
        }

        .text-base {
            font-size: 1rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        /* Spacing utilities */
        .space-y-4>*+* {
            margin-top: 1rem;
        }

        .space-y-1>*+* {
            margin-top: 0.25rem;
        }

        /* Mobile optimizations */
        @media (max-width: 767px) {
            .shadow-sm {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }

            /* Reduced line height for better mobile reading */
            .text-gray-700 {
                line-height: 1.5;
            }

            /* Improved touch targets */
            .mb-2 {
                margin-bottom: 0.75rem;
            }
        }

        /* Desktop styles */
        @media (min-width: 768px) {
            .text-md-xl {
                font-size: 1.5rem;
            }

            .text-md-lg {
                font-size: 1.125rem;
            }

            .text-md-base {
                font-size: 1rem;
            }

            .space-y-md-6>*+* {
                margin-top: 1.5rem;
            }

            .space-y-md-2>*+* {
                margin-top: 0.5rem;
            }

            /* Normal line height for desktop */
            .text-gray-700 {
                line-height: 1.6;
            }
        }

        /* Additional responsive improvements */
        .rounded-lg {
            border-radius: 0.5rem;
        }

        .text-gray-700 {
            color: #374151;
        }

        /* Improve readability */
        p {
            margin-bottom: 0;
        }

        /* Better spacing for nested content */
        div>p:last-child {
            margin-bottom: 0;
        }
    </style>

</div>
