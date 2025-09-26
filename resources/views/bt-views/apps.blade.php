@extends('template_bt')
@section('konten')
<div x-data="apps">
    
    <div class="mb-6 flex flex-wrap items-center justify-end gap-4 lg:justify-end">
        <button type="button" class="btn btn-primary flex" @click="add()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 ltr:mr-3 rtl:ml-3">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Data
        </button>
        
    </div>
    
    <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[black]/60 px-4" :class="isAddNoteModal && '!block'">
        <div class="flex min-h-screen items-center justify-center">
            <div x-show="isAddNoteModal" x-transition="" x-transition.duration.300="" @click.outside="isAddNoteModal = false" class="panel my-8 w-[90%] max-w-lg overflow-hidden rounded-lg border-0 p-0 md:w-full">
                <button type="button" class="absolute top-4 text-white-dark hover:text-dark ltr:right-4 rtl:left-4" @click="isAddNoteModal = false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                <div class="bg-[#fbfbfb] py-3 text-lg font-medium ltr:pl-5 ltr:pr-[50px] rtl:pr-5 rtl:pl-[50px] dark:bg-[#121c2c]" x-text="params.id ? 'Edit Aplikasi' : 'Tambah Aplikasi'"></div>
                <div class="p-5">
                    <form @submit.prevent="saveApp">
                        
                        <div class="mb-5">
                            <label for="jenis">Jenis Aplikasi</label>
                            <select id="jenis" class="form-select" x-model="params.jenis">
                                <option value="web">Web</option>
                                <option value="android">Android</option>
                                
                            </select>
                        </div>
                        
                        <div class="mb-5">
                            <label for="title">Nama Aplikasi</label>
                            <input id="title" type="text" placeholder="Masukkan Nama" class="form-input" x-model="params.name">
                        </div>
                        
                        <div class="mb-5">
                            <label for="typee">Type</label>
                            <select id="typee" class="form-select" x-model="params.typee">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                
                            </select>
                        </div>
                        
                        <div class="mb-5">
                            <label for="urllogin">URL Login</label>
                            <input id="urllogin" type="text" placeholder="Masukkan URL Login" class="form-input" x-model="params.urllogin" @focus="if (!params.urllogin) params.urllogin = 'https://'">
                        </div>
                        
                        <div class="mb-5">
                            <label>Logo</label>
                            <input id="ctnFile" type="file" class="rtl:file-ml-5 form-input p-0 file:border-0 file:bg-primary/90 file:py-2 file:px-4 file:font-semibold file:text-white file:hover:bg-primary ltr:file:mr-5" 
                                    @change="encodeImageFileAsURL" accept="image/*" x-model="params.foto">
                        </div>
                        
                        <input type="hidden" x-model="params.namafile_foto">
                        
                        <!-- Image Preview -->
                        <div class="mb-5" x-show="showImage">
                            <img :src="previewUrl" alt="Preview" class="w-32 h-32 rounded-lg shadow">
                        </div>
                        
                        <div class="mb-5">
                            <label for="desc">Deskripsi</label>
                            <textarea id="desc" rows="3" class="form-textarea min-h-[130px] resize-none" placeholder="Masukkan Deskripsi" x-model="params.des"></textarea>
                        </div>
                        <div class="mt-8 flex items-center justify-end">
                            <button type="button" class="btn btn-outline-danger gap-2" @click="isAddNoteModal = false">
                                Kembali
                            </button>
                            <button type="submit" class="btn btn-primary ltr:ml-4 rtl:mr-4" x-text="params.id ? 'Update' : 'Tambah'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="panel mt-6">
        <h5 class="mb-5 text-lg font-semibold dark:text-white-light md:absolute md:top-[25px] md:mb-0">List Aplikasi</h5>
        <table id="myTable" class="whitespace-nowrap"></table>
    </div>
</div>
@endsection