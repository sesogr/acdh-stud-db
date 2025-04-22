import { open } from "node:fs/promises";
import { pipeline } from "node:stream/promises";

const tokenise = (s: string) => {
  console.log("IN:", s);
  let token;
  const result = [];
  while (s.length > 0) {
    token = s.match(/(--)|(\s+)|(`)|(')|([(),;])/);
    result.push(token ? token[0] : s[0]);
    s = token ? s.substring(token[0].length) : s.substring(1);
  }
  if (result[result.length - 1].match(/[\w\s]$/)) {
    s += result.pop();
  }
  console.log("OUT:", result, s);
  return [result.join("\n"), s];
};
Promise.all([
  open("../../sql/student-similarity-birthrange.sql"),
  open(
    "../../sql/03-additions-2023/dump/20250316-student-similarity-graph-birthrange.csv",
    "w",
  ),
]).then(([inFile, outFile]) => {
  let buffer = "";
  return pipeline(
    inFile.createReadStream({ end: 580, highWaterMark: 128 }),
    async function* (source) {
      let chunk: Buffer;
      for await (chunk of source) {
        const [result, bufferNew] = tokenise(buffer + chunk.toString());
        // console.log("OUTER", [result, bufferNew]);
        buffer = bufferNew;
        yield result;
      }
    },
    outFile.createWriteStream(),
  );
});
