<x-app-layout>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-zinc-100 pb-20 pt-20 sm:pt-24">

        <div
            class="relative overflow-hidden bg-white border-b border-gray-100 shadow-sm mb-12 py-16 px-4 sm:px-6 lg:px-8">
            <div
                class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-40">
            </div>

            <div class="relative max-w-7xl mx-auto text-center">
                <span
                    class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 mb-4 tracking-wide uppercase">
                    <i class="fa-solid fa-shield-halved text-xs"></i> Directrices y Normativa
                </span>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight sm:text-5xl lg:text-6xl">
                    Políticas de Reserva y Cancelación
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-lg sm:text-xl text-gray-600 leading-relaxed">
                    Conoce nuestras directrices diseñadas para garantizar una experiencia justa, puntual y de máxima
                    calidad para todos.
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (isset($policies) && count($policies) > 0)
                @php $policy = $policies[0]; @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">

                    <div
                        class="group bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-indigo-100 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 bg-gradient-to-br from-indigo-50 to-indigo-100 p-3.5 rounded-xl text-indigo-600 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-calendar-days text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold tracking-wider text-indigo-600 uppercase mb-1">Planificación
                                </p>
                                <h3 class="text-sm font-semibold text-gray-500">Anticipación Máxima</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    Hasta {{ $policy['max_days_in_advance'] }} días antes
                                </p>
                                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Puedes planificar y apartar tu
                                    cita con esta antelación máxima.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-blue-100 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 bg-gradient-to-br from-blue-50 to-blue-100 p-3.5 rounded-xl text-blue-600 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-clock text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold tracking-wider text-blue-600 uppercase mb-1">Disponibilidad
                                </p>
                                <h3 class="text-sm font-semibold text-gray-500">Anticipación Mínima</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    {{ $policy['min_hours_before_booking'] }} horas antes
                                </p>
                                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Tiempo mínimo requerido para
                                    agendar un nuevo turno de última hora.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-amber-100 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 bg-gradient-to-br from-amber-50 to-amber-100 p-3.5 rounded-xl text-amber-600 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-ban text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold tracking-wider text-amber-600 uppercase mb-1">Flexibilidad
                                </p>
                                <h3 class="text-sm font-semibold text-gray-500">Límite para Cancelar</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    {{ $policy['cancel_before_hours'] }} horas antes
                                </p>
                                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Evita cargos o inconvenientes
                                    cancelando dentro de este plazo establecido.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 {{ $policy['allow_client_cancellation'] ? 'bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600' : 'bg-gradient-to-br from-rose-50 to-rose-100 text-rose-600' }} p-3.5 rounded-xl shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <i
                                    class="fa-solid {{ $policy['allow_client_cancellation'] ? 'fa-user-check' : 'fa-user-lock' }} text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-xs font-bold tracking-wider {{ $policy['allow_client_cancellation'] ? 'text-emerald-600' : 'text-rose-600' }} uppercase mb-1">
                                    Autogestión</p>
                                <h3 class="text-sm font-semibold text-gray-500">Panel del Cliente</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    {{ $policy['allow_client_cancellation'] ? 'Permitido' : 'No Disponible' }}
                                </p>
                                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Capacidad del cliente para
                                    cancelar directamente desde su panel personal.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-purple-100 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 bg-gradient-to-br from-purple-50 to-purple-100 p-3.5 rounded-xl text-purple-600 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-hourglass-start text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold tracking-wider text-purple-600 uppercase mb-1">Puntualidad
                                </p>
                                <h3 class="text-sm font-semibold text-gray-500">Periodo de Gracia</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    {{ $policy['grace_period_minutes'] }} minutos
                                </p>
                                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Tolerancia máxima de espera a
                                    partir de la hora pactada de tu cita.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="group bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-rose-100 transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 bg-gradient-to-br from-rose-50 to-rose-100 p-3.5 rounded-xl text-rose-600 shadow-inner group-hover:scale-110 transition-transform duration-300">
                                <i class="fa-solid fa-user-slash text-xl"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold tracking-wider text-rose-600 uppercase mb-1">Automatización
                                </p>
                                <h3 class="text-sm font-semibold text-gray-500">Inasistencia Automática</h3>
                                <p class="mt-1 text-xl font-bold text-gray-900">
                                    {{ $policy['mark_no_show_after_grace'] ? 'Activado' : 'Desactivado' }}
                                </p>
                                <p class="mt-2 text-xs text-gray-500 leading-relaxed">Marcar de forma automática como
                                    inasistencia tras expirar el periodo de gracia.</p>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-200/80 shadow-sm">
                    <div class="inline-flex bg-gray-50 p-4 rounded-full text-gray-400 mb-3">
                        <i class="fa-solid fa-folder-open text-3xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No hay políticas de reserva configuradas actualmente.</p>
                </div>
            @endif
        </div>

    </div>

</x-app-layout>
