dojoConfig = {
  async: true,
  baseUrl: "lib_dojo/",
  packages: [{
    name: "dojo",
    location: "dojo"
  },{
    name: "dojox",
    location: "dojox"
  }],
  deps: ["export"]
};

require("./lib_dojo/dojo/dojo.js");


