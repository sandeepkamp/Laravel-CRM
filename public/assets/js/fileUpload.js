(function ($) {
    var fileUploadCount = 0;
    var signers = [];
    var carbonCopies = [];
    var file_base64 = '';
    var file_name = '';
    var accessToken = '';
    var accountid = '';
    $(".close_button").on('click', function () {
        
        close_modal('modal_div');
    });

    function show_modal(modal_div){ 
        console.log("#"+modal_div+" clicked");
        $("#"+modal_div).removeClass("modal fade");
        $("#"+modal_div).addClass("modal show");
        $("#"+modal_div).css("display", "block");
        $("#"+modal_div).removeAttr("aria-hidden");
        $("#"+modal_div).attr("aria-modal", "true");
    }

    function close_modal(modal_div) {
        // alert(modal_div);
        $("#" + modal_div).removeClass("modal show");
        $("#" + modal_div).addClass("modal fade");
        $("#" + modal_div).css("display", "none");
        $("#" + modal_div).removeAttr("aria-modal");
        $("#" + modal_div).attr("aria-hidden", "true");
    }
    $("#docusign_button").on('click', function () {
        // alert('Docusign for the file');
        close_modal('modal_div');
        show_modal('docusign_div');

    });
    $("#add_recipient").on('click', function () { 
        $("#recipient_container").append(`
            <div class="row formsubmit">
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline">

                        <input type="text" class="form-control name" required name="name[]" id="floatingInput" placeholder="Enter name" aria-describedby="floatingInputHelp">
                        <label for="floatingInput">Name</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline">

                        <input type="email" class="form-control email" required name="email[]" id="floatingemail" placeholder="Enter email" aria-describedby="floatingemailHelp">
                        <label for="floatingemail">Email</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline">
                        <select id="selectpickerBasic" class="form-select  w-100 role" name="role[]" required data-style="btn-default" tabindex="null">
                            <option value="sign">Needs to sign</option>
                            <option value="copy">Receives a copy</option>
                        </select>

                    </div>

                </div>
                <div class="col-md-3">
                    <button type="button" class="delete_btn btn btn-outline-primary">Delete</button>

                </div>
                
                <hr class="mt-3"/>
            </div>
        `);
    });
 function pdfshow(pdf){

      let scale = 0.2; // Scale for thumbnails (adjust as needed)
      let pdfDoc = null;
      
      let pageNum = 1; // Current page being displayed
      let pageNum2 = 1;
      const base64 = pdf.split(',')[1];
      const pdfData = atob(base64);
      file_base64 = base64;

    pdfjsLib.getDocument({ data: pdfData }).then(pdf => {
    pdfDoc = pdf;
    const numPages = pdf.numPages;
    // Clear previous thumbnails
    document.getElementById('sidebar').innerHTML = '';
    // Prepare sidebar thumbnails
    const sidebar = document.getElementById('sidebar');
    for (let i = 1; i <= numPages; i++) {
      pdf.getPage(i).then(page => {
        // console.log(page);
        const viewport = page.getViewport({ scale });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        const renderContext = {
          canvasContext: context,
          viewport: viewport
        };
        page.render(renderContext).promise.then(() => {
          const thumbnail = canvas;
          thumbnail.classList.add('thumbnail');
          thumbnail.setAttribute('data-page-number', i);
          thumbnail.addEventListener('click', () => {
            displayPage(i);
            renderPage(i);
          });
          sidebar.appendChild(thumbnail);
        });
     
      });
    }
    // Display the initial page
    displayPage(1);
    renderPage(1);
    
  });

  function displayPage(pageNumber) {
    pageNum = pageNumber;

    // Fetch the specified page
    pdfDoc.getPage(pageNumber).then(page => {
      const viewport = page.getViewport({ scale });
      const canvas = document.getElementById('pdfCanvas');
      const context = canvas.getContext('2d');
      canvas.height = viewport.height;
      canvas.width = viewport.width;

      // Clear previous canvas content
      context.clearRect(0, 0, canvas.width, canvas.height);

      // Render PDF page into canvas context
      const renderContext = {
        canvasContext: context,
        viewport: viewport
      };

      page.render(renderContext);
    });

    // Update selected thumbnail style
    const thumbnails = document.querySelectorAll('.thumbnail');
      thumbnails.forEach(thumbnail => {
        const pageNumber1 = parseInt(thumbnail.getAttribute('data-page-number'));
        if (pageNumber === pageNumber1) {
          thumbnail.classList.add('selected');
        } else {
          thumbnail.classList.remove('selected');
        }
      });
    }
    
  function renderPage(num) {
      pdfDoc.getPage(num).then(function(page) {
        const viewport2 = page.getViewport({ scale:1 });
        console.log(viewport2);
        const canvas2 = document.createElement('canvas');
        const context2 = canvas2.getContext('2d');
        canvas2.height = viewport2.height;
        canvas2.width = viewport2.width;

        // Prepare canvas using PDF.js
        const renderContext = {
          canvasContext: context2,
          viewport: viewport2
        };
        page.render(renderContext);

        // Clear previous page
        pdfViewer.innerHTML = '';
        pdfViewer.appendChild(canvas2);

        // Update current page display
        $('#currentPage').text(num);
      });
    }

    }
    function createtoken(){
        $.ajax({
            url:"/create-docusign-token",
            method:"post",
             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success:function(res){
                accessToken = res.accessToken;
                accountid = res.accountid;
                createenvelope();
            }
        });
    }
    function createenvelope(){
        $("#loader-overlay").show(); // Show loader
        let documents = {
            "documentBase64": file_base64,
            "documentId": 123,
            "fileExtension": "pdf",
            "name": $('#file_name').val()
        };
        let object = {
            "documents": [documents],
            "emailSubject": $(".emailsubject").val(),
            "emailBlurb": $(".emailbody").val(),
            "recipients": {
                "signers": signers,
                "carbonCopies": carbonCopies
            },
            "status": "created"
        };
        $.ajax({
            url: '/create-envelope',
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(object),
            success: function(res){
                createviewsender(res.envelopeId);
            },
            error: function() {
                $("#loader-overlay").hide();
                alert('Failed to create envelope.');
            }
        });
    }

    function createviewsender(envelopeId){
        const formData = new FormData();
        var file = document.getElementById('fileUpload-1').files[0];
        formData.append('formFile', file);
        formData.append('envelopeId', envelopeId);
        formData.append('deal_id', $('#deal_id').val());
        formData.append('file_name', $('#file_name').val());
    
        $('#recipient_container .formsubmit').each(function () {
            formData.append('name[]', $(this).find('input.name').val());
            formData.append('email[]', $(this).find('input.email').val());
            formData.append('role[]', $(this).find('select.role').val());
        });
    
        $.ajax({
            url: '/view-sender',
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(res){
                $("#loader-overlay").hide(); // Hide loader
                if(res.url){
                    $("#docusignFrame").attr("src", res.url);
                    close_modal('email_body');
                    show_modal('docusign_div_sign_frame');
                    // Start polling status
                    pollEnvelopeStatus(res.envelopeId); 
                } else {
                    alert("Could not get DocuSign URL.");
                }
            },
            error: function() {
                $("#loader-overlay").hide();
                alert("Failed to create view sender.");
            }
        });
    }
    
    function pollEnvelopeStatus(envelopeId) {
        setInterval(() => {
            $.ajax({
                url: `/envelope-status/${envelopeId}`,
                method: "GET",
                success: function (res) {
                    if (res.total !== undefined) {
                        $("#envelopeStatusDisplay").text(`${res.completed}/${res.total} completed`);
                        
                        if (res.completed === res.total) {
                            $("#docusign_success_modal").modal('show');
                        }
                    }
                },
                error: function () {
                    console.error("Failed to fetch envelope status.");
                }
            });
        }, 10000); // Poll every 10 seconds
    }
    
    $(document).on('click', '.view-docusign-details', function () {
        const envelopeId = $(this).data('envelope-id');
        const $tbody = $('#docusignDetailsBody');
    
        $tbody.html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
    
        if (!envelopeId) {
            $tbody.html('<tr><td colspan="3" class="text-danger text-center">Invalid envelope ID</td></tr>');
            return;
        }
    
        $.ajax({
            url: `/envelope-status/${envelopeId}`,
            method: 'GET',
            success: function (res) {
                if (res.details && (res.details.signers || res.details.carbonCopies)) {
                    let rows = '';
    
                    const renderRecipientRow = (recipient, role) => {
                        const status = recipient.status || '';
                        const statusClass = status.toLowerCase() === 'completed' ? 'text-success' : 'text-primary';
                        return `
                            <tr>
                                <td>
                                    <strong>${recipient.name || ''}</strong><br>
                                    <small class="text-muted">${recipient.email || ''}</small>
                                </td>
                                <td>${role}</td>
                                <td class="${statusClass} text-capitalize fw-semibold">${status}</td>
                            </tr>`;
                    };
    
                    if (res.details.signers) {
                        res.details.signers.forEach(signer => {
                            rows += renderRecipientRow(signer, 'Need to sign');
                        });
                    }
    
                    if (res.details.carbonCopies) {
                        res.details.carbonCopies.forEach(cc => {
                            rows += renderRecipientRow(cc, 'Receives a copy');
                        });
                    }
    
                    $tbody.html(rows);
                } else {
                    $tbody.html('<tr><td colspan="3" class="text-center text-muted">No recipients found.</td></tr>');
                }
            },
            error: function () {
                $tbody.html('<tr><td colspan="3" class="text-danger text-center">Failed to load recipient data.</td></tr>');
            }
        });
    });



    $(document).on('click','#edit_docusign',function(){
        if(!$('.emailsubject').val()){
            if($('.emailsubject').siblings('.required').length == 0){
                $(`<span class="required text-danger">Required</span>`).insertAfter($('.emailsubject'));
            }else{
                $(``).insertAfter($('.emailsubject').val());
            }
         }
         createtoken();
    });
    $(document).on("keyup",".emailsubject",function(index,value){
         if(this.value){
            if($(this).siblings('.required').length == 1){
               $(this).siblings('.required').remove();
            }
        }else{
             if($(this).siblings('.required').length == 0){
                $(`<span class="required text-danger">Required</span>`).insertAfter(value);
            }
        }
    });

    $(document).on('click','.delete_btn', function () {
        $(this).parent().parent().remove();
        
    });
    $(document).on("keyup",".name",function(index,value){
      
        if(this.value){
            if($(this).siblings('.required').length == 1){
               $(this).siblings('.required').remove();
            }
        }else{
             if($(this).siblings('.required').length == 0){
                $(`<span class="required text-danger">Required</span>`).insertAfter(value);
            }
        }
    });
    $(document).on("keyup",".email",function(index,value){
         if(this.value){
            if($(this).siblings('.required').length == 1){
               $(this).siblings('.required').remove();
            }
        }else{
             if($(this).siblings('.required').length == 0){
                $(`<span class="required text-danger">Required</span>`).insertAfter(value);
            }
        }
    });
    $("#edit_message").on('click', function () {
         signers = [];
        carbonCopies = [];
        var validation  = 0;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        $('.name').each(function(index,value){
                if(!$(value).val()){
                    validation++;
                    if($(value).siblings('.required').length == 0){
                      $(`<span class="required text-danger">Required</span>`).insertAfter(value);
                    }
                }else{
                    $(``).insertAfter(value);
                }
        });
        $('.email').each(function(index,value){
                if(!$(value).val()){
                    validation++;
                    if($(value).siblings('.required').length == 0){
                      $(`<span class="required text-danger">Required</span>`).insertAfter(value);
                    }
                }else{
                    $(``).insertAfter(value);
                }
                if(!emailRegex.test($(value).val())){
                    validation++;
                    if($(value).siblings('.required').length == 0){
                      $(`<span class="required text-danger">Enter a Valid Email</span>`).insertAfter(value);
                    }else{
                        $(``).insertAfter(value);
                    }
                }
        });

        if(validation>0){
            return false;
        }else{
            $('.formsubmit').each(function(index,value){
              
                var object = {
                        name:$(value).children('.col-md-3').children('.form-floating').find('.name').val(),
                        email:$(value).children('.col-md-3').children('.form-floating').find('.email').val(),
                        recipientId:(index+1)
                    };
                if($(value).children('.col-md-3').children('.form-floating').find('.role').val() == 'sign'){
                    signers.push(object);        
                }else if($(value).children('.col-md-3').children('.form-floating').find('.role').val() == 'copy'){
                    carbonCopies.push(object);
                }
            });

                close_modal('docusign_div');
                show_modal('email_body');
            // console.log(signers,carbonCopies);
        }
    });

    $.fn.fileUpload = function () {
        return this.each(function () {
            var fileUploadDiv = $(this);
            var fileUploadId = `fileUpload-${++fileUploadCount}`;

            // Creates HTML content for the file upload area.
            // var fileDivContent = `
            //     <label for="${fileUploadId}" class="file-upload">
            //         <div>
            //             <i class="material-icons-outlined">cloud_upload</i>
            //             <p>Drag & Drop Files Here</p>
            //             <span>OR</span>
            //             <div>Browse Files</div>
            //         </div>
            //         <input type="file" id="${fileUploadId}" name=[] multiple hidden />
            //     </label>
            // `;

            // fileUploadDiv.html(fileDivContent).addClass("file-container");

           
            var tableBody = null;
            // Creates a table containing file information.
           

            // Adds the information of uploaded files to table.
            function handleFiles(file) {

                // alert('file uplaodded');
                if (file && file.type === 'application/pdf') {
                      
                      $('#file_name').val(file.name.split('.').slice(0, -1).join('.'));
                      var fileToLoad = file;
                      var fileReader = new FileReader();
                      fileReader.onload = function(fileLoadedEvent) {
                        var srcData = fileLoadedEvent.target.result; // <--- data: base64

                           // console.log(srcData);
                            // var objectURL = URL.createObjectURL(file);
                            // console.log(objectURL);
                            pdfshow(srcData);
                            $("#modal_div").removeClass("modal fade");
                            $("#modal_div").addClass("modal show");
                            $("#modal_div").css("display","block");
                            $("#modal_div").removeAttr("aria-hidden");
                            $("#modal_div").attr("aria-modal","true");
                            // document.getElementById('showpdfid').data = srcData;
          
                      }
                      fileReader.readAsDataURL(fileToLoad);

                    // console.log(file);
                    // var objectURL = URL.createObjectURL(file);
                    // console.log(objectURL);
                    // $("#modal_div").removeClass("modal fade");
                    // $("#modal_div").addClass("modal show");
                    // $("#modal_div").css("display","block");
                    // $("#modal_div").removeAttr("aria-hidden");
                    // $("#modal_div").attr("aria-modal","true");
                    // document.getElementById('showpdfid').data = objectURL;
                }
                else { 
                    alert("somehgting other file");
                }
                
            }

            // Events triggered after dragging files.
            fileUploadDiv.on({
                dragover: function (e) {
                    e.preventDefault();
                    fileUploadDiv.toggleClass("dragover", e.type === "dragover");
                },
                drop: function (e) {
                    e.preventDefault();
                    fileUploadDiv.removeClass("dragover");
                    handleFiles(e.originalEvent.dataTransfer.files[0]);
                },
            });

            // Event triggered when file is selected.
            fileUploadDiv.find(`#${fileUploadId}`).change(function () {
                handleFiles(this.files[0]);
            });
        });
    };
})(jQuery);
