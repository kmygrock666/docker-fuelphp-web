require.config({
    baseUrl: ".",
    paths: {
        "autobahn": "assets/js/autobahn",
        // "autobahn": "http://autobahn.s3.amazonaws.com/js/autobahn",
        // "when": "//cdnjs.cloudflare.com/ajax/libs/when/2.7.1/when",
        "jquery-3.4.1": "assets/js/jquery-3.4.1",
        "bootstrap.bundle": "assets/js/bootstrap.bundle",
        "bootstrap.bundle.min": "assets/js/bootstrap.bundle.min",
        "custom": "assets/js/custom",
        "moment": "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.25/moment.min",
        "daterangepicker": "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.25/daterangepicker.min"

    }
    // packages: [
    //     { name: 'when', location: 'assets/js/when-master/', main: 'when' }
    // ],
    // shim: {
        // "autobahn": {
        //     deps: ["when"]
        // }
    // }
});
require([
        "jquery-3.4.1",
        "bootstrap.bundle",
        "bootstrap.bundle.min",
        "custom",
        "moment",
        "daterangepicker",
        "autobahn"
        ], function(autobahn) {
            // console.log("autobahn","when");
    });
