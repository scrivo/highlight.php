dojoConfig = {
  async: true,
  baseUrl: "lib/",
  packages: [{
    name: "dojo",
    location: "dojo"
  },{
    name: "dijit",
    location: "dijit"
  },{
    name: "dojox",
    location: "dojox"
  }],
  deps: ["export"]
};

require("./lib/dojo/dojo.js");


