<div>

    @php
        $disabledProperty = true;
        $disabledPropertyRjStatus = false;
    @endphp
    {{-- jika anamnesa kosong ngak usah di render --}}
    {{-- @if (isset($dataDaftarPoliRJ['diagnosis'])) --}}
    <div x-data="{ open: false }" class="w-full mb-1 ">

        <div id="TransaksiRawatJalan" class="p-2">
            <div id="TransaksiRawatJalan">

                <div class="p-2 rounded-lg bg-gray-50">
                    <div class="grid grid-cols-1">

                        <div id="TransaksiRawatJalan" class="px-4">
                            <x-input-label for="" :value="__('Non Racikan')" :required="__(false)" class="pt-2 sm:text-xl" />

                            {{-- Non Racikan --}}
                            <div>

                                {{-- xx --}}
                                <div class="mt-3 w-96" x-data="dropdownMovies()" x-init="$watch('movie', () => selectedMovieIndex = '')">
                                    <div>
                                        <input type="text"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            placeholder="Search" x-model="movie" x-on:click.outside="reset()"
                                            x-on:keyup.escape="reset()" x-on:keyup.down="selectNextMovie"
                                            x-on:keyup.up="selectPreviousMovie" x-on:keyup.enter="goToUrl()">

                                        <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                                            x-show="filteredMovies.length>0" x-transition x-ref="movies">
                                            <template x-for="(movie, index) in filteredMovies">
                                                <button x-text="movie.name" class="block w-full px-4 py-2 text-left"
                                                    :class="{ 'bg-gray-100 outline-none': index === selectedMovieIndex }"
                                                    x-on:click.prevent="goToUrl(movie)"></button>
                                            </template>
                                        </div>
                                        <div class="block px-4 py-2 mt-1 bg-white border rounded-md shadow-gray-50"
                                            x-show="movie!=='' && filteredMovies.length===0">
                                            No Movies Available.
                                        </div>
                                    </div>
                                </div>
                                {{-- xx --}}
                                <x-input-label for="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang"
                                    :value="__('Waktu Datang')" :required="__(true)" />

                                <div class="mb-2 ">
                                    <div class="grid grid-cols-5 gap-2 mb-2 ">
                                        <x-text-input id="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang"
                                            placeholder="Waktu Datang [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2"
                                            :errorshas="__(
                                                $errors->has('dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang'),
                                            )" :disabled=$disabledPropertyRjStatus
                                            wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang" />

                                        <x-text-input id="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang"
                                            placeholder="Waktu Datang [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2"
                                            :errorshas="__(
                                                $errors->has('dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang'),
                                            )" :disabled=$disabledPropertyRjStatus
                                            wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang" />

                                        <x-text-input id="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang"
                                            placeholder="Waktu Datang [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2"
                                            :errorshas="__(
                                                $errors->has('dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang'),
                                            )" :disabled=$disabledPropertyRjStatus
                                            wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang" />

                                        <x-text-input id="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang"
                                            placeholder="Waktu Datang [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2"
                                            :errorshas="__(
                                                $errors->has('dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang'),
                                            )" :disabled=$disabledPropertyRjStatus
                                            wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang" />

                                        <x-text-input id="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang"
                                            placeholder="Waktu Datang [dd/mm/yyyy hh24:mi:ss]" class="mt-1 ml-2"
                                            :errorshas="__(
                                                $errors->has('dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang'),
                                            )" :disabled=$disabledPropertyRjStatus
                                            wire:model.debounce.500ms="dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang" />
                                    </div>

                                </div>
                                @error('dataDaftarPoliRJ.anamnesa.pengkajianPerawatan.jamDatang')
                                    <x-input-error :messages=$message />
                                @enderror
                            </div>



                            <div class="flex">
                                <div class="flex items-center justify-end w-full mr-5">
                                    <x-text-input placeholder="Isi dgn data yang sesuai"
                                        class="mt-1 ml-2 sm:rounded-none sm:rounded-l-lg" :errorshas="__($errors->has('SEPJsonReq.request.t_sep.diagAwal'))"
                                        :disabled=$disabledProperty value="{{ 'Masukkan Diagnosa ICD 10' }}" />

                                    <x-green-button :disabled=false
                                        class="mt-1 sm:rounded-none sm:rounded-r-lg sm:mb-0 sm:mr-0 sm:px-2"
                                        wire:click.prevent="clickdataDiagnosaICD10lov()">
                                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path clip-rule="evenodd" fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                    </x-green-button>
                                    @error('SEPJsonReq.request.t_sep.diagAwal')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>
                            {{-- LOV Diagnosa --}}
                            <div class="relative mt-0 bg-red-300">
                                @include('livewire.emr-r-j.mr-r-j.diagnosis.list-of-value-caridataDiagnosaICD10')
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="flex flex-col my-2">
                            <div class="overflow-x-auto rounded-lg">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden shadow sm:rounded-lg">
                                        <table
                                            class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3">


                                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                                            href="#">
                                                            Kode (ICD 10)
                                                        </x-sort-link>

                                                    </th>

                                                    <th scope="col" class="px-4 py-3 ">
                                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                                            href="#">
                                                            Diagnosa
                                                        </x-sort-link>
                                                    </th>

                                                    <th scope="col" class="px-4 py-3">

                                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                                            href="#">
                                                            Keterangan Diagnosa
                                                        </x-sort-link>
                                                    </th>

                                                    <th scope="col" class="px-4 py-3">

                                                        <x-sort-link :active=false wire:click.prevent="" role="button"
                                                            href="#">
                                                            Kategori
                                                        </x-sort-link>
                                                    </th>

                                                    <th scope="col" class="w-8 px-4 py-3 text-center">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800">

                                                @foreach ($dataDaftarPoliRJ['diagnosis'] as $key => $diag)
                                                    <tr class="border-b group dark:border-gray-700">

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $diag['icdX'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $diag['diagDesc'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $diag['ketdiagnosa'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $diag['kategoriDiagnosa'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                                            <x-alternative-button class="inline-flex"
                                                                wire:click.prevent="removeDiagICD10('{{ $diag['diagId'] }}')">
                                                                <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 18 20">
                                                                    <path
                                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                </svg>
                                                                {{ 'Hapus ' . $diag['icdX'] }}
                                                            </x-alternative-button>

                                                        </td>




                                                    </tr>
                                                @endforeach



                                            </tbody>
                                        </table>







                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Racikan --}}
                        <x-input-label for="" :value="__('Racikan')" :required="__(false)" class="pt-2 sm:text-xl" />
                        <div>
                            <div class="flex">
                                <div class="flex items-center justify-end w-full mr-5">
                                    <x-text-input placeholder="Isi dgn data yang sesuai"
                                        class="mt-1 ml-2 sm:rounded-none sm:rounded-l-lg" :errorshas="__($errors->has('SEPJsonReq.request.t_sep.diagAwal'))"
                                        :disabled=$disabledProperty value="{{ 'Masukkan Procedure ICD 9 CM' }}" />

                                    <x-green-button :disabled=false
                                        class="mt-1 sm:rounded-none sm:rounded-r-lg sm:mb-0 sm:mr-0 sm:px-2"
                                        wire:click.prevent="clickdataProcedureICD9Cmlov()">
                                        <svg class="-ml-1 mr-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path clip-rule="evenodd" fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                        </svg>
                                    </x-green-button>
                                    @error('SEPJsonReq.request.t_sep.diagAwal')
                                        <x-input-error :messages=$message />
                                    @enderror
                                </div>
                            </div>
                            {{-- LOV Diagnosa --}}
                            <div class="relative mt-0 bg-red-300">
                                @include('livewire.emr-r-j.mr-r-j.diagnosis.list-of-value-caridataProcedureICD9Cm')
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="flex flex-col my-2">
                            <div class="overflow-x-auto rounded-lg">
                                <div class="inline-block min-w-full align-middle">
                                    <div class="overflow-hidden shadow sm:rounded-lg">
                                        <table
                                            class="w-full text-sm text-left text-gray-500 table-auto dark:text-gray-400">
                                            <thead
                                                class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3">


                                                        <x-sort-link :active=false wire:click.prevent=""
                                                            role="button" href="#">
                                                            Kode (ICD 9 CM)
                                                        </x-sort-link>

                                                    </th>

                                                    <th scope="col" class="px-4 py-3 ">
                                                        <x-sort-link :active=false wire:click.prevent=""
                                                            role="button" href="#">
                                                            Prosedur
                                                        </x-sort-link>
                                                    </th>

                                                    <th scope="col" class="px-4 py-3">

                                                        <x-sort-link :active=false wire:click.prevent=""
                                                            role="button" href="#">
                                                            Keterangan Prosedur
                                                        </x-sort-link>
                                                    </th>

                                                    {{-- <th scope="col" class="px-4 py-3">
            
                                                                <x-sort-link :active=false wire:click.prevent="" role="button"
                                                                    href="#">
                                                                    Kategori
                                                                </x-sort-link>
                                                            </th> --}}

                                                    <th scope="col" class="w-8 px-4 py-3 text-center">
                                                        Action
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800">


                                                @foreach ($dataDaftarPoliRJ['procedure'] as $key => $procedure)
                                                    <tr class="border-b group dark:border-gray-700">

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $procedure['procedureId'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $procedure['procedureDesc'] }}
                                                        </td>

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                            {{ $procedure['ketProcedure'] }}
                                                        </td>

                                                        {{-- <td
                                                                    class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">
                                                                    {{ $procedure['kategoriprocedurenosa'] }}
                                                                </td> --}}

                                                        <td
                                                            class="px-4 py-3 font-normal text-gray-700 group-hover:bg-gray-50 whitespace-nowrap dark:text-white">

                                                            <x-alternative-button class="inline-flex"
                                                                wire:click.prevent="removeProcedureICD9Cm('{{ $procedure['procedureId'] }}')">
                                                                <svg class="w-5 h-5 text-gray-800 dark:text-white"
                                                                    aria-hidden="true"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    fill="currentColor" viewBox="0 0 18 20">
                                                                    <path
                                                                        d="M17 4h-4V2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2H1a1 1 0 0 0 0 2h1v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V6h1a1 1 0 1 0 0-2ZM7 2h4v2H7V2Zm1 14a1 1 0 1 1-2 0V8a1 1 0 0 1 2 0v8Zm4 0a1 1 0 0 1-2 0V8a1 1 0 0 1 2 0v8Z" />
                                                                </svg>
                                                                {{ 'Hapus ' . $procedure['procedureId'] }}
                                                            </x-alternative-button>

                                                        </td>




                                                    </tr>
                                                @endforeach



                                            </tbody>
                                        </table>







                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>






                </div>

            </div>
        </div>
    </div>





</div>
{{-- @endif --}}



<div>
    <nav x-data="{ open: false }" class="bg-green-300 border-b border-gray-100">
        <!-- Primary Navigation Menu -->
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <div class="mt-3 w-96" x-data="dropdownMovies()" x-init="$watch('movie', () => selectedMovieIndex = '')">
                    <div>
                        <input type="text"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Search" x-model="movie" x-on:click.outside="reset()"
                            x-on:keyup.escape="reset()" x-on:keyup.down="selectNextMovie"
                            x-on:keyup.up="selectPreviousMovie" x-on:keyup.enter="goToUrl()">

                        <div class="py-2 mt-1 overflow-y-auto bg-white border rounded-md shadow-lg max-h-64"
                            x-show="filteredMovies.length>0" x-transition x-ref="movies">
                            <template x-for="(movie, index) in filteredMovies">
                                <button x-text="movie.name" class="block w-full px-4 py-2 text-left"
                                    :class="{ 'bg-gray-100 outline-none': index === selectedMovieIndex }"
                                    x-on:click.prevent="goToUrl(movie)"></button>
                            </template>
                        </div>
                        <div class="block px-4 py-2 mt-1 bg-white border rounded-md shadow-gray-50"
                            x-show="movie!=='' && filteredMovies.length===0">
                            No Movies Available.
                        </div>
                    </div>
                </div>




            </div>
    </nav>

    @push('scripts')
        <script>
            function dropdownMovies() {
                return {
                    movie: "",
                    selectedMovieIndex: "",
                    movies: [{
                            id: 1,
                            name: "Spider-Man: No Way Home"
                        },
                        {
                            id: 2,
                            name: "Eternals"
                        },
                        {
                            id: 3,
                            name: "Hotel Transylvania: Transformania"
                        },
                        {
                            id: 4,
                            name: "Ghostbusters: Afterlife"
                        },
                        {
                            id: 5,
                            name: "The Matrix Resurrections"
                        },
                        {
                            id: 6,
                            name: "The Ice Age Adventures of Buck Wild"
                        },
                        {
                            id: 7,
                            name: "Venom: Let There Be Carnage"
                        },
                        {
                            id: 8,
                            name: "Red Notice"
                        },
                        {
                            id: 9,
                            name: "Shang-Chi and the Legend of the Ten Rings"
                        },
                    ],

                    get filteredMovies() {
                        if (this.movie === "") {
                            return [];
                        }

                        return this.movies.filter(movie => movie.name.toLowerCase().includes(this.movie.toLowerCase()))
                    },

                    reset() {
                        this.movie = "";
                    },

                    selectNextMovie() {
                        if (this.selectedMovieIndex === "") {
                            this.selectedMovieIndex = 0;
                        } else {
                            this.selectedMovieIndex++;
                        }

                        if (this.selectedMovieIndex === this.filteredMovies.length) {
                            this.selectedMovieIndex = 0;
                        }

                        this.focusSelectedMovie();
                    },

                    selectPreviousMovie() {
                        if (this.selectedMovieIndex === "") {
                            this.selectedMovieIndex = this.filteredMovies.length - 1;
                        } else {
                            this.selectedMovieIndex--;
                        }

                        if (this.selectedMovieIndex < 0) {
                            this.selectedMovieIndex = this.filteredMovies.length - 1;
                        }

                        this.focusSelectedMovie();
                    },

                    focusSelectedMovie() {
                        this.$refs.movies.children[this.selectedMovieIndex + 1].scrollIntoView({
                            block: 'nearest'
                        })
                    },

                    goToUrl(movie) {
                        let currentMovie = movie ? movie : this.filteredMovies[this.selectedMovieIndex];

                        window.location = `/dashboard?name=${currentMovie.name}`;
                    },
                };
            }
        </script>
    @endpush
</div>
