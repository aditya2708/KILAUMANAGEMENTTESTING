@extends('template_bt')
@section('konten')
<div x-data="akses">
    
    
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
        <div class="panel">
            <div class="mb-6 flex flex-wrap items-center justify-end gap-4 lg:justify-end">
                <button type="button" class="btn btn-primary flex" @click="add()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 ltr:mr-3 rtl:ml-3">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Data
                </button>
            </div>
            <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">Modul</h5>
            <table id="myTable" class="whitespace-nowrap" ></table>
        </div>
        
        <div class="panel">
            <div class="mb-6 flex flex-wrap items-center justify-end gap-4 lg:justify-end">
                <button type="button" class="btn btn-primary flex" @click="add()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 ltr:mr-3 rtl:ml-3">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah Data
                </button>
            </div>
            <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">Role</h5>
            <table id="myTable1" class="whitespace-nowrap"></table>
        </div>
    </div>
    
</div>
@endsection