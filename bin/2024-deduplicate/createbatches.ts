import { createConnection, Connection, UpsertResult } from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import {
  loadBatchOfPropertyRecords,
  writeComparisonBatch,
} from "./database";
import fs from 'node:fs';
import fsPromises from 'node:fs/promises'
import { exit } from "node:process";
const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};
const BATCH_SIZE = 1024;

createConnection(credentials).then((connection) =>
  getHighestAvailableIds(connection)
    .then((limits) => findBatchIds(connection, limits, BATCH_SIZE))
    .then((ids) => {
      console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1]);
      fs.writeFileSync("ids.json",JSON.stringify(ids) + "\n", { flag: 'a+' });      
      return ids;
    })
    .then(() => connection.end()).catch((message) => console.error(message)));


createConnection(credentials).then((connection) =>
  getnextavailableIds()
    .then((limits) => findBatchIds(connection, limits, BATCH_SIZE))
    .then((ids) => {
      console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1]);
      fs.writeFileSync("ids.json",JSON.stringify(ids) + "\n", { flag: 'a+' });      
      return ids;
    })
    .then(() => connection.end()).catch((message) => console.error(message)));


type GetNextAvailableIds = () => Promise<[number, number, number]>;
const getnextavailableIds:GetNextAvailableIds = () => 
  fsPromises.readFile("ids.json","utf-8").then((file) => {
    const filebyLines = file.split("\n");
    const lastline = filebyLines[filebyLines.length -1]
    let length = lastline.length;
    let line = lastline.substring(1,length - 1).split(",")
    
    return [[filebyLines[0]],[line[0],line[line.length - 1]]];
  }).then(
    ([[end], [low, high]]) =>
      [low || 0, high || 0, end || 0].map((n) => +n) as [
        number,
        number,
        number,
      ],
  );



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
  if(!fs.existsSync("ids.json")){
    fs.writeFileSync("ids.json", max + "\n")
  }
  return connection
    .query(
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