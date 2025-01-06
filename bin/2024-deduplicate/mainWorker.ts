
import { Connection } from 'mariadb';
import { Worker } from 'worker_threads'; 
  
function runService(workerData: [number[],connection:Connection]) { 
    return new Promise((resolve, reject) => { 
        const worker = new Worker( 
                './worker.js', { workerData }); 
        worker.on('message', resolve); 
        worker.on('error', reject); 
        worker.on('exit', (code) => { 
            if (code !== 0) 
                reject(new Error( 
            `Stopped the Worker Thread with the exit code: ${code}`)); 
        }) 
    }) 
} 
  
export function run(workerData:number[],connection:Connection) { 
    const result = runService([workerData, connection]); 
    console.log(result); 
} 
