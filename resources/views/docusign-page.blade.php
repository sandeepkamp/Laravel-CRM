<!DOCTYPE html>
<html>
  <head>

    <meta charset="utf-8" />
    <title>Signing</title>

    <style>
      Html,
      body {
        padding: 0;
        margin: 0;
        font: 13px Helvetica, Arial, sans-serif;
      }
      .docusign-agreement {
        width: 100%; 
        height: 800px;
      }
    </style>
     @php
     header("X-Frame-Options: ALLOWALL"); 
     @endphp

  </head>
  <body>
    <header><h1> Demo</h1></header>
    <div class="docusign-agreement" id="agreement"></div>
    <script src = "https://js-d.docusign.com/bundle.js"> </script>
<!-- When this script is loaded, DocuSign is added to the window -->
<!-- use src="https://js.docusign.com/bundle.js" for production -->
<script>
 window.DocuSign.loadDocuSign('c39e98c7-0c0f-43f4-acf5-ea167332fa54').then((docusign) => {
 	console.log(docusign);
  const signing = docusign.signing({
    url: 'https://apps-d.docusign.com/api/send/auth-from-console-1ds?code=66dfc120-3962-4ba8-b938-c8d48146689e&t=6037647a-de88-4763-9388-71cd43a1b458&from=https%3A%2F%2Fdemo.docusign.net&r=http%3A%2F%2F127.0.0.1%3A8000%2F&view=true&DocuEnvelope=aad16741-7563-4688-8070-82a9cda0261e&e=aad16741-7563-4688-8070-82a9cda0261e&send=1&accountId=11b202e8-2611-4356-8ca2-abd1ef521e0a&a=tag&vt=5',
    displayFormat: 'default',
    style: {

      /** High-level variables that mirror our existing branding APIs. Reusing the branding name here for familiarity. */
      branding: {
        primaryButton: {
          /** Background color of primary button */
          backgroundColor: '#333',
          /** Text color of primary button */
          color: '#fff',
        }
      },

      /** High-level components we allow specific overrides for */
      signingNavigationButton: {
        finishText: 'Custom Button Text',
        /** 'bottom-left'|'bottom-center'|'bottom-right',  default: bottom-right */
        position: 'bottom-left'
      }
    }
  });

  /** Event handlers **/
  signing.on('ready', (event) => {
    console.log('UI is rendered');
  });
  signing.on('sessionEnd', (event) => {
    /** The event here denotes what caused the sessionEnd to trigger, such as signing_complete, ttl_expired etc../ */
    window.top.location.href = event.returnUrl;
    console.log('sessionend', event);
  });

  signing.mount('#agreement');
}).catch((ex) => {
  // Any configuration or API limits will be caught here
});
</script>
  </body>
</html>