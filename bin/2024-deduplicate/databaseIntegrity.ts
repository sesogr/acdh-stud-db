import { getAllIds, getIdLow } from "./database";
import { createConnection } from "mariadb";

const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};

// let allids = new Set(getAllids().map((e) => e[0]));
// let actallids = new Set(actualids.map((e) => e[0]));
// let diff = [...allids].filter((x) => !actallids.has(x));
// console.log(diff);

createConnection(credentials).then((connection) => {
  Promise.all([
    getAllIds(connection),
    getIdLow(connection, "student_similarity_graph"),
  ]).then((results) => {
    let allids = results[0];
    console.log(allids);
  });
});
