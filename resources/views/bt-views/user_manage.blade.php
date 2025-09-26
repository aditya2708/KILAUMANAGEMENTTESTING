@extends('template_bt')
@section('konten')
<div x-data="users">
    
    <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[black]/60 px-4" :class="{'!block':addUserModal}">
        <div class="flex min-h-screen items-center justify-center">
            <div x-show="addUserModal" x-transition="" x-transition.duration.300="" @click.outside="addUserModal = false" class="panel my-8 w-[90%] max-w-lg overflow-hidden rounded-lg border-0 p-0 md:w-full">
                <button type="button" class="absolute top-4 text-white-dark hover:text-dark ltr:right-4 rtl:left-4" @click="addUserModal = false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div class="bg-[#fbfbfb] py-3 text-lg font-medium ltr:pl-5 ltr:pr-[50px] rtl:pr-5 rtl:pl-[50px] dark:bg-[#121c2c]" >Hak Akses User</div>
                <div class="p-5">
                    <form @submit.prevent="saveUsers">
                        <div class="mb-5">
                            <label for="nama">Nama</label>
                            <input id="nama" type="text" placeholder="Enter Name" class="form-input" x-model="params.nama" readonly>
                        </div>
                        
                        <div class="mb-5">
                            <label for="email">Email</label>
                            <input id="email" type="email" placeholder="Enter Email" class="form-input" x-model="params.email" readonly>
                        </div>
                        
                        <div class="mb-5">
                            <label >Hak Akses</label>
                        </div>
                        
                        <div class="mb-5 flex justify-between gap-4">
                            <div class="flex-1 mt-1">
                                <h4>CMS Kilau</h4>
                            </div>
                            <div class="flex-1">
                                <select id="cmskilau" class="form-select" x-model="params.cmskilau">
                                    <option value="">Pilih Akses</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        
                        <!--<div class="mb-5 flex justify-between gap-4">-->
                        <!--    <div class="flex-1 mt-1">-->
                        <!--        <h4>TimKita</h4>-->
                        <!--    </div>-->
                        <!--    <div class="flex-1">-->
                        <!--        <select id="timkita" class="form-select" x-model="params.timkita">-->
                        <!--            <option value="">Pilih Akses</option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="mt-8 flex items-center justify-end ltr:text-right rtl:text-left">
                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel mt-6">
        <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">List User</h5>
        <table id="myTable" class="whitespace-nowrap"></table>
    </div>
</div>
@endsection