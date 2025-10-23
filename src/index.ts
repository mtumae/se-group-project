import { eq } from 'drizzle-orm';
import { drizzle } from 'drizzle-orm/neon-http';
import {orders} from "./db/schema"
const db = drizzle(import.meta.env.VITE_NEON_DATABASE_URL);



interface OrderType {
    userid:string,
    itemName:string,
    itemId:string,
    itemImageUrl:string,
    quantity:number,
}


export async function getAllOrders(){
    const data =  await db
    .select()
    .from(orders)
    .where(eq(orders.userid, 'mtume1234'));


    return data;
}


