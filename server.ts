
//import 'dotenv/config';
import { drizzle } from 'drizzle-orm/neon-http';
//import { eq } from 'drizzle-orm';
import {user} from '@/db/schema';


const db = drizzle(import.meta.env.VITE_DATABASE_URL!);



export default async function getUsers(){
    const data = await db.select().from(user);
    return data ?? null
}


