import { integer, pgTable, timestamp, varchar } from "drizzle-orm/pg-core";




export const orders = pgTable("orders", {
    id: integer().primaryKey().generatedAlwaysAsIdentity(),
    userid: varchar({ length: 255 }).notNull(),
    itemName: varchar({ length: 255 }).notNull(),
    itemId: varchar({ length: 255 }).notNull(),
    itemImageUrl: varchar({ length: 255 }).notNull(),
    quantity: integer().notNull(),
    orderDate: timestamp('orderDate').notNull(),
    status: varchar({ length: 255 }).notNull(),
});



