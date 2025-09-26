@extends('template')
@section('konten')
<div class="content-body">
    <div class="container-fluid">


           <div class="mb-3 row">
                    <div class="col-sm-9">
                        <select class="form-control input-sm" name="plh" id="plh">
                                <option value="">- Pilih -</option>
                                @foreach($data as $val)
                                <option value="{{$val->token}}" data-value="{{$val->name}}">{{$val->name}}</option>
                                @endforeach
                        </select>
                    </div>
            </div>
                                        
                                        
                                        
                         <div class="col-md-12">
                            <label >Pesan:</label>
                            <input type="text" name="pesan" id="pesan" class="form-control" placeholder="" required>
                        </div>
                                                
                        <!--<div class="modal-footer">-->
                             <button type="button" class="btn btn-success btn-sm update"  >Simpan</button>
                            <!--<button type="submit" class="btn btn-primary blokkk" id="smpn">Simpan</button>-->
                        <!--</div>-->
        
    </div>
</div>
@endsection