import { defineSchema, defineTable } from "convex/server";
import { v } from "convex/values";
import { authTables } from "@convex-dev/auth/server";


export default defineSchema({
  ...authTables,
  users:defineTable({
    email:v.string(),
    fullName:v.optional(v.string()),
    role:v.optional(v.string()),
  }),

  items: defineTable({
    userId: v.string(),
    imageUrl: v.string(),
    itemName: v.string(),
    itemDescription: v.string(),
    price: v.string(),
    category: v.string(),
    createdAt: v.string(),
  }).searchIndex("itemName", {
    searchField:'itemName',
    filterFields:['category', 'price']
  }),
  
  orders: defineTable({
    userId: v.string(),
    itemId: v.string(),
    itemName: v.string(),
    quantity: v.number(),
    orderDate: v.string(),
    status: v.string(),
  }).index('byUserId', ['userId'])
  ,
  

});



