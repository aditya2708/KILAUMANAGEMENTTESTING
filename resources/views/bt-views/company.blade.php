@extends('template_bt')
@section('konten')
<div x-data="pagination">
    
    
    <div class="mb-6 flex flex-wrap items-center justify-end gap-4 lg:justify-end">
        <button type="button" class="btn btn-primary" @click="add">
            <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ltr:mr-2 rtl:ml-2">
                <circle cx="10" cy="6" r="4" stroke="currentColor" stroke-width="1.5"></circle>
                <path opacity="0.5" d="M18 17.5C18 19.9853 18 22 10 22C2 22 2 19.9853 2 17.5C2 15.0147 5.58172 13 10 13C14.4183 13 18 15.0147 18 17.5Z" stroke="currentColor" stroke-width="1.5"></path>
                <path d="M21 10H19M19 10H17M19 10L19 8M19 10L19 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
            </svg>
            Tambah Data
        </button>
    </div>
    
    <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[black]/60" :class="addContactModal && '!block'">
        <div class="flex min-h-screen items-center justify-center px-4" @click.self="addContactModal = false">
            <div x-show="addContactModal" x-transition="" x-transition.duration.300="" class="panel my-8 w-[90%] max-w-lg overflow-hidden rounded-lg border-0 p-0 md:w-full">
                <button type="button" class="absolute top-4 text-white-dark hover:text-dark ltr:right-4 rtl:left-4" @click="addContactModal = false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <h3 class="bg-[#fbfbfb] py-3 text-lg font-medium ltr:pl-5 ltr:pr-[50px] rtl:pr-5 rtl:pl-[50px] dark:bg-[#121c2c]" x-text="params.id ? 'Edit' : 'Tambah'"></h3>
                <div class="p-5">
                    <form @submit.prevent="saveUser">
                        <div class="mb-5">
                            <label for="name">Name</label>
                            <input id="name" type="text" placeholder="Enter Name" class="form-input" x-model="params.name">
                        </div>
                        <div class="mt-8 flex items-center justify-end">
                            <button type="button" class="btn btn-outline-danger" @click="addContactModal = false">
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
        <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">List Perushaan</h5>
        <table id="myTable1" class="table-hover whitespace-nowrap"></table>
    </div>
</div>
@endsection