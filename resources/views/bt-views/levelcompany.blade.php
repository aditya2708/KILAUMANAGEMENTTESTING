@extends('template_bt')
@section('konten')
<div  x-data="entryLevelForm()">
    
 <div class="mb-6 flex flex-wrap items-center justify-end gap-4 lg:justify-end">
        <button type="button" class="btn btn-primary" @click="addLevelModal = true">
            <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ltr:mr-2 rtl:ml-2">
                <circle cx="10" cy="6" r="4" stroke="currentColor" stroke-width="1.5"></circle>
                <path opacity="0.5" d="M18 17.5C18 19.9853 18 22 10 22C2 22 2 19.9853 2 17.5C2 15.0147 5.58172 13 10 13C14.4183 13 18 15.0147 18 17.5Z" stroke="currentColor" stroke-width="1.5"></path>
                <path d="M21 10H19M19 10H17M19 10L19 8M19 10L19 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
            </svg>
            Tambah Data
        </button>
    </div>
    
    <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[black]/60" :class="addLevelModal && '!block'">
        <div class="flex min-h-screen items-center justify-center px-4" @click.self="addLevelModal = false">
            <div x-show="addLevelModal" x-transition="" x-transition.duration.300="" class="panel my-8 w-[90%] max-w-lg overflow-hidden rounded-lg border-0 p-0 md:w-full">
                <button type="button" class="absolute top-4 text-white-dark hover:text-dark ltr:right-4 rtl:left-4" @click="addLevelModal = false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <h3 class="bg-[#fbfbfb] py-3 text-lg font-medium ltr:pl-5 ltr:pr-[50px] rtl:pr-5 rtl:pl-[50px] dark:bg-[#121c2c]" >Tambah</h3>
                <div class="p-5">
                    <form @submit.prevent="saveLevel()">
                        <div class="mb-5">
                            <label for="apk">Jumlah Apk</label>
                            <input id="jumlah_apk" type="number" placeholder="Enter Jumlah Apk" class="form-input" x-model="params.data.listApk.jumlah_apk">
                        </div>
                        <div class="mb-5">
                            <label for="level">Jumlah Level</label>
                            <input id="jumlah_level" type="number" placeholder="Enter Jumlah Level" class="form-input" x-model="params.data.level.jumlah_level">
                        </div>
                        <div class="mt-8 flex items-center justify-end">
                            <button type="button" class="btn btn-outline-danger" @click="addLevelModal = false">
                                Kembali
                            </button>
                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    

    
    <div class="panel mt-6">
        

       <form @submit.prevent="submitForm">
            <label class="text-lg mb-5 mt-5">Company</label>
            
            <div class="flex items-center gap-5" x-data="{ isChecked: 1 == {{$company->auto_aktivasi}} }">
                <label class="text-sm mb-5 mt-5">Auto Aktivasi</label>
                <label class="w-12 h-6 relative">
                    <input
                        type="checkbox"
                        class="custom_switch absolute w-full h-full opacity-0 z-10 cursor-pointer peer"
                        x-model="isChecked"
                        @change="updateStatus(isChecked)"
                    />
                    <span class="outline_checkbox bg-icon border-2 border-[#ebedf2] dark:border-white-dark block h-full rounded-full
                        before:absolute before:left-1 before:bg-[#ebedf2] dark:before:bg-white-dark
                        before:bottom-1 before:w-4 before:h-4 before:rounded-full
                        before:bg-[url('../images/close.svg')] before:bg-no-repeat before:bg-center
                        peer-checked:before:left-7 peer-checked:before:bg-[url('../images/checked.svg')]
                        peer-checked:border-primary peer-checked:before:bg-primary
                        before:transition-all before:duration-300">
                    </span>
                </label>
            </div>
            
          <!--<template>-->
                <div
                     x-data="{ waktuTrial: '{{ $dataTrial->waktu }}' }"
                    class="flex gap-5 mb-5 transition-all duration-300 ease-in-out"
                    x-transition:enter="opacity-0 translate-y-2"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="opacity-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                >
                    <div 
                        class="flex-none w-25" 
                    >
                        <label class="block text-sm font-medium mb-1">Waktu Trial</label>
                        <input type="hidden">
                        <div class="flex items-center">
                            <input 
                                type="text" 
                                class="form-input h-10" 
                                placeholder="1"
                                x-model="waktuTrial"
                            >
                            <label class="block text-sm font-medium ml-3">Bulan</label>
                        </div>
                    </div>

                </div>
            <!--</template>-->
            <template class="mb-5" x-if="listCompany.length === 0 || listCompany.every(item => item.company === null)">
                <div class="flex gap-5 mb-5">
                    <!-- Input Jumlah Perusahaan -->
                    <div class="flex-none w-50">
                        <label class="block text-sm font-medium mb-1">Jumlah Company</label>
                        <input
                            type="number"
                            placeholder="Jumlah"
                            class="form-input h-10 w-full"
                            x-model="newCompany.company"
                        >
                    </div>
            
                    <!-- Input Harga Per Bulan -->
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Harga/Bln</label>
                        <input
                            type="number"
                            placeholder="Harga/Bln"
                            class="form-input h-10 w-full"
                            x-model="newCompany.harga"
                        >
                    </div>
                </div>
            </template>
            <template class="mb-5" x-for="(item, index) in listCompany" :key="item.id ?? index">
                <!-- TAMPILKAN HANYA DATA YANG company != null -->
                <div
                    class="flex gap-5 mb-5 transition-all duration-300 ease-in-out"
                    x-transition:enter="opacity-0 translate-y-2"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="opacity-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                >
                    <div class="flex-none w-50">
                        <label class="block text-sm font-medium mb-1">Company</label>
                        <input type="hidden" x-model="item.id">
                         <input type="text" placeholder="Jumlah" class="form-input h-10" x-model="item.company">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Harga/Bulan</label>
                        <input type="text" placeholder="Harga/Bln" class="form-input h-10" x-model="item.harga">
                    </div>
                </div>
            </template>


            <label class="text-lg mb-5 mt-5">Aplikasi</label>
            <div class="flex gap-5 mt-5">
                <div class="flex-1">
                    <label for="level">Nama</label>
                </div>
                <div class="flex-1">
                     <label for="level">Versi</label>
                </div>
                <div class="flex-1">
                    <label for="level">Harga/Bulan</label>
                </div>
            </div>
            <template x-for="(item, index) in listApk" :key="item.id ?? index">
                <div
                    class="flex gap-5 mb-5 transition-all duration-300 ease-in-out"
                    x-transition:enter="opacity-0 translate-y-2"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="opacity-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                >
                    <div class="flex-none w-25">
                        <input type="hidden" x-model="item.id">
                        <input type="text" placeholder="Nama" class="form-input h-10" x-model="item.nama_apk">
                    </div>
                    <div class="flex-1">
                        <input type="text" placeholder="Versi" class="form-input h-10" x-model="item.versi_apk">
                    </div>
                    <div class="flex-1">
                        <input type="number" placeholder="Harga" class="form-input h-10" x-model="item.harga_apk">
                    </div>
                </div>
            </template>
            
            
            <label class="text-lg mb-5 mt-5">Karyawan</label>
            
        <div class="flex gap-5 mt-5">
            <div class="flex-1">
                <label for="level">Level</label>
            </div>
            <div class="flex-1">
                 <label for="level">Jumlah karyawan</label>
            </div>
            <div class="flex-1">
                <label for="level">Harga</label>
            </div>
        </div>

            <template x-for="(item, index) in list" :key="item.id ?? index">
                <div
                    class="flex gap-5 mb-5 transition-all duration-300 ease-in-out"
                    x-transition:enter="opacity-0 translate-y-2"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="opacity-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                >
                    <div class="flex-none w-25">
                        <input type="hidden" x-model="item.id">
                        <input type="number" placeholder="Level" class="form-input h-10" x-model="item.level">
                    </div>
                    <div class="flex-1">
                        <input type="number" placeholder="Karyawan" class="form-input h-10" x-model="item.karyawan">
                    </div>
                    <div class="flex-1">
                        <input type="number" placeholder="Harga" class="form-input h-10" x-model="item.harga">
                    </div>
                </div>
            </template>

        
            <div class="mt-6 text-right w-full">
                <button type="submit" class="btn btn-primary px-4 py-2">Simpan</button>
            </div>
        </form>

    </div>
</div>
@endsection


@push('scripts')
    @include('bt-js.pricing')
@endpush