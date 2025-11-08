import { defineSchema, defineTable } from "convex/server";
import { v } from "convex/values";
import { authTables } from "@convex-dev/auth/server";


export default defineSchema({
  ...authTables,
  users:defineTable({
    email:v.string(),
    fullName:v.optional(v.string()),
    role:v.optional(v.string()),
  }).searchIndex("fullName", {
    searchField:'fullName'
  })
  ,

  items: defineTable({
    userId: v.string(),
    username: v.string(),
    imageUrl: v.optional(v.string()),
    link:v.optional(v.string()),//for faker items
    itemName: v.string(),
    itemDescription: v.string(),
    price: v.number(),
    category: v.string(),
    createdAt: v.string(),
    disabled: v.optional(v.boolean()),
  }).searchIndex("itemName", {
    searchField:'itemName',
    filterFields:['category'],
    staged: false,
  }),
  
  orders: defineTable({
    userId: v.string(),
    username: v.string(),
    itemId: v.string(),
    itemName: v.string(),
    orderDate: v.string(),
    status: v.string(),
  }).index('byUserId', ['userId'])
  ,
  

});



