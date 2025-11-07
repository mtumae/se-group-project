import { getAuthUserId } from "@convex-dev/auth/server";
import { mutation, query } from "./_generated/server";
import { convexToJson, v } from "convex/values";

export const currentUser = query({
  handler: async (ctx) => {
    const userId = await getAuthUserId(ctx);
    if (userId === null) {
      return null;
    }
    return await ctx.db.get(userId);
  },
});


export const getAllUsers = query({
  handler: async (ctx) => {
    const users = await ctx.db.query("users").collect();
    return users;
  },
});


export const addUser= mutation({
  args: { 
    email:  v.string(),
    password: v.string(),
    fullName: v.optional(v.string()),
},
  handler: async (ctx, args) => {
    const user = await ctx.db.insert("users", {
      email: args.email,
      fullName: args.fullName,
    });
    return user;
  }
})