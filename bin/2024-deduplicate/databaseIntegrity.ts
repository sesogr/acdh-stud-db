import { getAllIds, getAllIdLow, getIdHighFromIdLow } from "./database";
import { Connection, createConnection } from "mariadb";
import fs from "node:fs";
import fsPromises from "node:fs/promises";
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
      fs.writeFileSync("ids.json", `[[${allIds[allIds.length - 1]}]]`, {
        flag: "w",
      });
      const allHighIds: number[][] = [];

      for (let i = 0; i < allIds.length; i++) {
        allHighIds.push([...allIds.slice(i)]);
      }
      loopingThroughAllLowIds(allHighIds, connection).then(() =>
        connection.end()
      );
    });
  });

async function loopingThroughAllLowIds(
  allHighIds: number[][],
  connection: Connection
) {
  for (let currentHighIds of allHighIds) {
    await getIdHighFromIdLow(connection, table, currentHighIds[0]).then(
      async (actualIdHighObject) => {
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
          const missingArray = [currentHighIds[0], ...missing];
          await updateJSON(missingArray);
        }
      }
    );
  }
  return Promise.resolve();
}

async function updateJSON(newArray: number[]) {
  const file = await fsPromises.readFile("ids.json", "ascii");
  let json: number[][] = JSON.parse(file);
  json.push(newArray);
  await fsPromises.writeFile("ids.json", JSON.stringify(json, null, 2), {
    flag: "w",
  });
}
