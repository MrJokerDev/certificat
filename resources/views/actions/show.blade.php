<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">


<div class="container pt-5">

        <div class="row">
            <div class="col-md-12 col-sm-12">
                @if(session()->get('delete'))
                    <div class="alert alert-danger  alert-dismissible fade show" role="alert" style="text-align: center">
                        {{ session()->get('delete') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(session()->get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="text-align: center">
                        {{ session()->get('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="card mb-3" style="max-width: 540px;">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <img class="img-fluid rounded-start" src="{{ asset('images/' . $students->image) }}" alt="fotoStudent">
                        </div>

                        <div class="col-md-6">
                            <div class="card-body">
                                <div class="given">
                                    @if ($students->status == '1')
                                        <span class="badge bg-danger">inActive</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif

                                    <h3>{{ $students->full_name }}</h3>
                                </div>
                                <p class="card-text">Student ID: <strong>{{ $students->id }}</strong></p>
                                <p class="card-text">Preseason: <strong>{{ $students->course }}</strong></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
			@if (Auth::user())
                <div id="qr" class="col-md-6 col-sm-12">
                    <div class="d-block img-qrcode">
                        <div id="qrcode" class="qrcode"></div>
                        <br>
                        <a class="btn btn-success" id='qrdl' hidden><i class="bi bi-download"></i></a>
                    </div>
                    <br><br>
                    <form action="{{ route('dashboard.store') }}" method="POST">
                        @csrf
                        <button type="submit" name="student_id" value="{{ $students->id }}" class="btn btn-success">Keldi</button>
                    </form>
                </div>
          	@endauth
        </div>
</div>

<script src="{{ asset('js/qrcode.js') }}"></script>
<script type="text/javascript">
    const makeQR = (url, filename) => {
        var url = "{{ route('dashboard.show', $students->id )}}"
        var qrcode = new QRCode("qrcode", {
            text: url,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        qrcode.makeCode(url);

        setTimeout(() => {
            let qelem = document.querySelector('#qrcode img')
            let dlink = document.querySelector('#qrdl')
            let qr = qelem.getAttribute('src');
            dlink.setAttribute('href', qr);
            dlink.setAttribute('download', 'filename');
            dlink.removeAttribute('hidden');
        }, 500);
        }

    makeQR('qrcode.png')
</script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
