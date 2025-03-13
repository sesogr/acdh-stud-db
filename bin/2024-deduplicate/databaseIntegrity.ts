import { getAllIds, getAllIdLow } from "./database";
import { createConnection } from "mariadb";

const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};

createConnection(credentials).then((connection) => {
  getAllIds(connection).then((allIdObjects) => {
    const allIds: number[] = allIdObjects.map(
      (element: { person_id: number }) => element.person_id
    );
    getAllIdLow(connection, "student_similarity_graph").then((idLowObjects) => {
      connection.end();
      const idLows: number[] = idLowObjects.map(
        (element: { id_low: number }) => element.id_low
      );
      const missingIds = allIds.filter((individualId) =>
        idLows.includes(individualId)
      );
      console.log(missingIds);
    });
  });
});

// createConnection(credentials).then((connection) => {
// getAllIds(connection).then((allIdObjects) => {
// const allIds: number[] = allIdObjects.map(
// (element: { person_id: number }) => element.person_id
// );
// const allidPairs: number[][] = allIds.map((e, i, array) => {
// return [e, ...array.slice(i)];
// });
// getAllIdLowHighPairs(
// connection,
// "student_similarity_graph_birthrange"
// ).then((idPairObjects) => {
// console.log(idPairObjects);
// });
// });
// });
