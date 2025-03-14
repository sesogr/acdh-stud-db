import { getAllIds, getAllIdLow, getIdHighFromIdLow } from "./database";
import { createConnection } from "mariadb";
import fs from "node:fs";
let starter = 1;
let table = "student_similarity_graph";
const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};

if (starter == 0)
  createConnection(credentials).then((connection) => {
    getAllIds(connection).then((allIdObjects) => {
      const allIds: number[] = allIdObjects.map(
        (element: { person_id: number }) => element.person_id
      );
      getAllIdLow(connection, table).then((idLowObjects) => {
        connection.end();
        const idLows: number[] = idLowObjects.map(
          (element: { id_low: string }) => parseInt(element.id_low)
        );
        const missing: number[] = allIds.filter(
          (individualId) => !idLows.includes(individualId)
        );
        if (missing.length > 1) {
          const missingArray: number[][] = [];
          missingArray.push([missing[missing.length - 1]]);
          for (let i = 0; i < missing.length; i++) {
            missingArray.push([...allIds.slice(i)]);
          }
          fs.writeFileSync("ids.json", JSON.stringify(missingArray));
        }
      });
    });
  });

if (starter == 1)
  createConnection(credentials).then((connection) => {
    getAllIds(connection).then((allIdObjects) => {
      const allIds: number[] = allIdObjects.map(
        (element: { person_id: number }) => element.person_id
      );
      const allHighIds: number[][] = [];

      for (let i = 0; i < allIds.length; i++) {
        allHighIds.push([...allIds.slice(i)]);
      }
      const missingArray: number[][] = [];
      missingArray.push([allIds[allIds.length - 1]]);
      Promise.all(
        allHighIds.map((currentHighIds) => {
          return getIdHighFromIdLow(connection, table, currentHighIds[0]);
        })
      ).then((actualIdHighObjects) =>
        allHighIds.forEach((currentHighIds) =>
          actualIdHighObjects.forEach((actualIdHighObject) => {
            const actualHighIds: number[] = actualIdHighObject.map(
              (element: { id_high: string }): number => {
                const idHigh: number = parseInt(element.id_high);
                return idHigh;
              }
            );
            const actualIdPairs = [currentHighIds[0], ...actualHighIds];
            const missing = currentHighIds.filter(
              (currentId) => !actualIdPairs.includes(currentId)
            );
            if (missing.length > 0) {
              console.log(missing);
              missingArray.push(missing);
            }
            fs.writeFileSync("ids.json", JSON.stringify(missingArray));
            connection.end();
          })
        )
      );
    });
  });
