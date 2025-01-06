import { createConnection, Connection } from "mariadb";
import fs from 'node:fs';
import { run } from "./mainWorker";
const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};
const BATCH_SIZE = 10;
export function get4batches(){
  createConnection(credentials).then((connection) =>
  getHighestAvailableIds(connection)
    .then((limits) => findBatchIds(connection, limits, BATCH_SIZE))
    .then((ids) => {
      console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1]);
      run(ids, connection);
      return ids;
    })
    .then((ids) =>
      loopinggetnextavaialbleIds(connection,ids)
    )
    .then(() => connection.end()).catch((message) => console.error(message)));
}




async function loopinggetnextavaialbleIds(connection:Connection,lastids:number[]) {
  let max = parseInt(fs.readFileSync("max.json","ascii"));
  for (let i = 0; i < 3; i++) {
      await getnextavailableIds(lastids,max).then((limits) => findBatchIds(connection, limits, BATCH_SIZE))
      .then((ids) => {
        console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1]);
        run(ids,connection);
        return ids;
      })
  }
}
function getnextavailableIds(lastids:number[],max:number){
  return new Promise<[number,number,number]>(() => {
    return [lastids[0],lastids[lastids.length-1],max]
  })
}
type FindBatchIds = (
  connection: Connection,
  highestAvailableIds: [number, number, number],
  maxSize: number,
) => Promise<number[]>;
const findBatchIds: FindBatchIds = (
  connection,
  highestAvailableIds,
  maxSize,
) => {
  const [left, right, max] = highestAvailableIds;
  if(!fs.existsSync("max.json")){
    fs.writeFileSync("max.json", max + "")
  }
  return connection.query(
      {
        rowsAsArray: true,
        bigIntAsNumber: true,
        // language=MariaDB
        sql:
          "(select person_id from student_identity where person_id >= ? limit 1)" +
          " union " +
          "(select person_id from student_identity where person_id > ? limit ?)",
      },
      [
        left + (right === max ? 1 : 0),
        right === max ? left + 1 : right,
        maxSize,
      ],
    )
    .then((result) => result.flat())
    .then((result) => {
      if (result.length < 2)
        throw new Error(
          "No more records after " + highestAvailableIds.join(":"),
        );
      else return result;
    });
};

type GetHighestAvailableIds = (
  connection: Connection,

) => Promise<[number, number, number]>;
const getHighestAvailableIds: GetHighestAvailableIds = (connection) =>
  connection
    .query({
      rowsAsArray: true,
      bigIntAsNumber: true,
      // language=MariaDB
      sql:
        "(select max(person_id), 0 from student_identity)" +
        " union " +
        "(select id_low, id_high from student_similarity_graph order by id_low desc, id_high desc limit 1)" +
        " union " +
        "(select 0, 0 from dual)",
    })
    // [["35678",  0],
    // [    2, 13],
    // [    0,  0]]
    .then(
      ([[end], [low, high]]) =>
        [low || 0, high || 0, end || 0].map((n) => +n) as [
          number,
          number,
          number,
        ],
    );

const jobQueue = <I, T, O>(
  collection: I[],
  job: (i: I) => Promise<T>,
  combine: (t: T, i: I) => O,
): Promise<O[]> =>
  collection.reduce(
    (accu: Promise<Awaited<O[]>>, item: I) =>
      accu.then((others) =>
        job(item).then((result) => [...(others as O[]), combine(result, item)]),
      ) as Promise<O[]>,
    Promise.resolve([] as O[]),
  ) as Promise<O[]>;